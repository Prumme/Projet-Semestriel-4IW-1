<?php

namespace App\Command;

use DateTime;
use App\Helper\URL;
use App\Entity\Invoice;
use App\Data\TemplatesList;
use App\Entity\Company;
use App\Service\EmailService;
use App\Service\URLSignedService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvoicePaymentReminder extends Command
{
    protected static $defaultName = 'app:invoice-payment-reminder';
    private $entityManager;
    private $sendinblueService;
    private $urlSignedService;

    public function __construct(EntityManagerInterface $entityManager, EmailService $sendinblueService, URLSignedService $urlSignedService)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->sendinblueService = $sendinblueService;
        $this->urlSignedService = $urlSignedService;
    }

    protected function configure()
    {
        $this->setDescription('Send payment reminders to customers.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(Invoice::class);
        $invoices = $repository->findBy(['status' => Invoice::STATUS_AWAITING_PAYMENT]);
        
        foreach ($invoices as $invoice) {
            $user = $invoice->getCustomer();
            $company = $invoice->getQuote()->getOwner()->getCompany();

            // Use send-in blue to send the email
            $to = $user->getEmail();
            $templateId = TemplatesList::PAYMENT_REMINDER;
            $signedUrl = $this->urlSignedService->signURL('app_invoice_paid', ['invoice' => $invoice->getId(), 'company' => $company->getId()]);

            $templateVariables = [
                'name' => $user->getFirstName(),
                'link' => $_ENV['IP'].$signedUrl,
            ];

            $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);
        }            

        $output->writeln('Payment reminder send successfully!');

        return Command::SUCCESS;
    }
}
