<?php

// Debug script to test form processing
require_once 'vendor/autoload.php';

// Load environment
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

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use App\Form\EvenementType;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\FormFactoryInterface;
use App\Entity\Evenement;
use App\Entity\User;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}

$kernel = new Kernel('dev', true);
$kernel->boot();

$container = $kernel->getContainer();
$formFactory = $container->get(FormFactoryInterface::class);

echo "=== Testing Evenement Form ===\n";

// Test Evenement form
$evenement = new Evenement();
$form = $formFactory->create(EvenementType::class, $evenement);

echo "Form fields:\n";
foreach ($form as $name => $field) {
    echo "- $name: " . get_class($field->getConfig()->getType()->getInnerType()) . "\n";
}

// Test with sample data
$formData = [
    'titre' => 'Test Event',
    'description' => 'This is a test event description that is long enough to meet the minimum requirements.',
    'dateDebut' => '2026-06-01T10:00',
    'dateFin' => '2026-06-01T14:00',
    'lieu' => 1, // This will need to be a valid Lieu entity
    'capaciteMax' => 50,
    'prix' => 0,
    'categorie' => 'conference',
    'tags' => []
];

$form->submit($formData);

echo "\nForm submission test:\n";
echo "Valid: " . ($form->isValid() ? 'YES' : 'NO') . "\n";
echo "Submitted: " . ($form->isSubmitted() ? 'YES' : 'NO') . "\n";

if (!$form->isValid()) {
    echo "\nForm errors:\n";
    foreach ($form->getErrors(true) as $error) {
        echo "- " . $error->getMessage() . "\n";
    }
}

echo "\n=== Testing Registration Form ===\n";

// Test Registration form
$user = new User();
$regForm = $formFactory->create(RegistrationFormType::class, $user);

echo "Registration form fields:\n";
foreach ($regForm as $name => $field) {
    echo "- $name: " . get_class($field->getConfig()->getType()->getInnerType()) . "\n";
}

$regData = [
    'email' => 'test@example.com',
    'pseudo' => 'TestUser',
    'plainPassword' => [
        'first' => 'password123',
        'second' => 'password123'
    ]
];

$regForm->submit($regData);

echo "\nRegistration form submission test:\n";
echo "Valid: " . ($regForm->isValid() ? 'YES' : 'NO') . "\n";
echo "Submitted: " . ($regForm->isSubmitted() ? 'YES' : 'NO') . "\n";

if (!$regForm->isValid()) {
    echo "\nRegistration form errors:\n";
    foreach ($regForm->getErrors(true) as $error) {
        echo "- " . $error->getMessage() . "\n";
    }
}
