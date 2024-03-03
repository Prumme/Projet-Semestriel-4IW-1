<?php

// src/Command/UpdateInvoiceStatusCommand.php

namespace App\Command;

use DateTime;
use App\Entity\Facture;
use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateInvoiceStatusCommand extends Command
{
    protected static $defaultName = 'app:update-invoice-status';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Update invoice status to unpaid if expired.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentDate = new DateTime();
        $repository = $this->entityManager->getRepository(Invoice::class);
        $invoices = $repository->findAll();

        foreach ($invoices as $invoice) {
            if ($invoice->getExpiredAt() < $currentDate) {
                $invoice->setStatus(Invoice::STATUS_UNPAID);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Invoice statuses updated successfully.');

        return Command::SUCCESS;
    }
}
