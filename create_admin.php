<?php

require_once 'vendor/autoload.php';

// Load environment variables
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        putenv(trim($line));
        $_ENV[substr($line, 0, strpos($line, '='))] = substr($line, strpos($line, '=') + 1);
    }
}

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}

$kernel = new Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get(EntityManagerInterface::class);
$passwordHasher = $container->get(UserPasswordHasherInterface::class);

// Create admin user
$admin = new User();
$admin->setEmail('admin@eventspot.fr');
$admin->setPseudo('Admin');
$admin->setRoles(['ROLE_ADMIN']);
$admin->setPassword($passwordHasher->hashPassword($admin, 'admin123'));

$entityManager->persist($admin);
$entityManager->flush();

echo "Admin user created successfully!\n";
echo "Email: admin@eventspot.fr\n";
echo "Password: admin123\n";
