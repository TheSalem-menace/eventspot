<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:test-email',
    description: 'Test email sending functionality'
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Testing email sending...</info>');

        try {
            $email = (new Email())
                ->from('noreply@eventspot.fr')
                ->to('test@example.com')
                ->subject('🧪 Test Email - EventSpot')
                ->html('
                    <h1>🎫 EventSpot - Test Email</h1>
                    <p>Ceci est un email de test pour vérifier que l\'envoi fonctionne.</p>
                    <p>Si vous recevez cet email, la configuration Mailtrap est correcte !</p>
                    <p>Envoyé le : ' . date('d/m/Y H:i:s') . '</p>
                ');

            $result = $this->mailer->send($email);
            
            $output->writeln('<success>✅ Email sent successfully!</success>');
            $output->writeln('<info>📬 Check your Mailtrap inbox</info>');
            $output->writeln('<info>🔗 https://mailtrap.io</info>');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $output->writeln('<error>❌ Error sending email: ' . $e->getMessage() . '</error>');
            $output->writeln('<comment>📝 Check your MAILER_DSN configuration in .env</comment>');
            
            return Command::FAILURE;
        }
    }
}
