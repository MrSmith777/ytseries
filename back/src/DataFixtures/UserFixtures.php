<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 *
 * @package App\DataFixtures
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $devUser = $this->makeUser('dev', 'dev', [GroupFixtures::GROUP_DEV]);
        $commonUser = $this->makeUser('user', 'user', [GroupFixtures::GROUP_USER]);

        $devUser->setSuperAdmin(true);

        $manager->persist($devUser);
        $manager->persist($commonUser);

        $manager->flush();

        $this->addReference('user-dev', $devUser);
        $this->addReference('user-user', $commonUser);
    }

    private function makeUser(string $name, string $pass, array $groups = []): User
    {
        $user = new User();
        $user
            ->setUsername($name)
            ->setEmail(sprintf('%s@local.com', $name))
            ->setEnabled(true);

        $password = $this->encoder->encodePassword($user, $pass);
        $user->setPassword($password);

        foreach ($groups as $key) {
            /** @var Group $group */
            $group = $this->getReference($key);
            $user->addGroup($group);
        }

        return $user;
    }

    public function getDependencies(): array
    {
        return [
            GroupFixtures::class,
        ];
    }
}
