<?php

namespace App\Controller;

use App\Entity\PushSubscription;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PushController extends AbstractController
{
    #[Route('/api/push/subscribe', name: 'api_push_subscribe', methods: ['POST'])]
    public function subscribe(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser] ?User $user
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['endpoint']) || !isset($data['keys']['p256dh']) || !isset($data['keys']['auth'])) {
            return new JsonResponse(['error' => 'Invalid subscription data'], 400);
        }

        $subscription = $entityManager->getRepository(PushSubscription::class)->findOneBy(['endpoint' => $data['endpoint']]);

        if (!$subscription) {
            $subscription = new PushSubscription();
            $subscription->setEndpoint($data['endpoint']);
        }

        $subscription->setP256dh($data['keys']['p256dh']);
        $subscription->setAuth($data['keys']['auth']);
        $subscription->setUser($user);

        $entityManager->persist($subscription);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
