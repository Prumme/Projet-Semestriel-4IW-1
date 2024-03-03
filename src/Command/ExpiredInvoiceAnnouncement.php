<?php

namespace App\Command;

use App\Helper\URL;
use App\Entity\Invoice;
use App\Data\TemplatesList;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExpiredInvoiceAnnouncement extends Command
{
    protected static $defaultName = 'app:expired-invoice-announcement';
    private $entityManager;
    private $sendinblueService;
    private $urlHelper;

    public function __construct(EntityManagerInterface $entityManager, EmailService $sendinblueService, URL $urlHelper)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->sendinblueService = $sendinblueService;
        $this->urlHelper = $urlHelper;
    }

    protected function configure()
    {
        $this->setDescription('Expired invoice announcement sent successfully!');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->entityManager->getRepository(Invoice::class);
        $invoices = $repository->findBy(['status' => Invoice::STATUS_UNPAID]);

        foreach ($invoices as $invoice) {
            $user = $invoice->getCustomer();
            $number = $invoice->getInvoiceNumber();
            
            $to = $user->getEmail();
            $templateId = TemplatesList::EXPIRED_INVOICE_ANNOUNCEMENT;

            $templateVariables = [
                'name' => $user->getFirstName(),
                'number' => $number,
            ];

            $this->sendinblueService->sendEmailWithTemplate($to, $templateId, $templateVariables);
        }

        $output->writeln('Expired invoice announcement sent successfully!');

        return Command::SUCCESS;
    }
}
