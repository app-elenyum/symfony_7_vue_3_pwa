<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\PushSubscription;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-push',
    description: 'Send a test push notification to a user',
)]
class SendTestPushCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private string $vapidPublicKey,
        private string $vapidPrivateKey
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error(sprintf('User with email "%s" not found.', $email));
            return Command::FAILURE;
        }

        $subscriptions = $user->getPushSubscriptions();
        if ($subscriptions->isEmpty()) {
            $io->warning('User has no push subscriptions.');
            return Command::FAILURE;
        }

        $auth = [
            'VAPID' => [
                'subject' => 'mailto:admin@vionis.ru',
                'publicKey' => $this->vapidPublicKey,
                'privateKey' => $this->vapidPrivateKey,
            ],
        ];

        $webPush = new WebPush($auth);

        $payload = json_encode([
            'title' => 'Vionis Test',
            'body' => 'Push notification received! Time: ' . date('H:i:s'),
            'icon' => '/icons/icon-192x192.png',
            'url' => '/'
        ]);

        $io->note(sprintf('Attempting to send to %d subscriptions...', count($subscriptions)));

        foreach ($subscriptions as $sub) {
            $subscription = Subscription::create([
                'endpoint' => $sub->getEndpoint(),
                'publicKey' => $sub->getP256dh(),
                'authToken' => $sub->getAuth(),
            ]);

            $webPush->queueNotification($subscription, $payload);
        }

        $anySuccess = false;
        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getEndpoint();
            if ($report->isSuccess()) {
                $io->success(sprintf('Sent successfully!'));
                $anySuccess = true;
            } else {
                $reason = $report->getReason();
                $io->error(sprintf('Failed: %s', $reason));
                
                // Auto-cleanup expired subscriptions
                if (str_contains($reason, '410 Gone') || str_contains($reason, '404 Not Found')) {
                    $subToDelete = $this->entityManager->getRepository(PushSubscription::class)->findOneBy(['endpoint' => $endpoint]);
                    if ($subToDelete) {
                        $this->entityManager->remove($subToDelete);
                        $io->note('Removed expired subscription from database.');
                    }
                }
            }
        }

        $this->entityManager->flush();

        return $anySuccess ? Command::SUCCESS : Command::FAILURE;
    }
}
