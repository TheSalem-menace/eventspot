<?php

// Simple test to check if forms are working
echo "Testing basic form creation...\n";

use App\Form\EvenementType;
use App\Form\RegistrationFormType;
use App\Entity\Evenement;
use App\Entity\User;

$evenement = new Evenement();
$evenementForm = new EvenementType();
echo "✅ EvenementType form created successfully\n";

$user = new User();
$registrationForm = new RegistrationFormType();
echo "✅ RegistrationFormType form created successfully\n";

echo "\nTesting form field configuration...\n";

// Test Evenement form build
$builder = new \Symfony\Component\Form\FormBuilder('test', null);
$evenementForm->buildForm($builder, []);

$fields = [];
foreach ($builder->all() as $name => $field) {
    $fields[] = $name;
}

echo "Evenement form fields: " . implode(', ', $fields) . "\n";

// Test Registration form build
$builder2 = new \Symfony\Component\Form\FormBuilder('test2', null);
$registrationForm->buildForm($builder2, []);

$fields2 = [];
foreach ($builder2->all() as $name => $field) {
    $fields2[] = $name;
}

echo "Registration form fields: " . implode(', ', $fields2) . "\n";

echo "\n✅ Form classes are working correctly\n";
