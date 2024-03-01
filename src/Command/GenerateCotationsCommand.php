<?php



namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\CotationGenerator;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Crypto;

class GenerateCotationsCommand extends Command
{
    private $cotationGenerator;
    private $entityManager;

    public function __construct(CotationGenerator $cotationGenerator, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->cotationGenerator = $cotationGenerator;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setName('app:generate-cotations')
            ->setDescription('Generate cotations for cryptocurrencies.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int

    {
        $cryptos = $this->entityManager->getRepository(Crypto::class)->findAll();

        foreach ($cryptos as $crypto) {
            $this->cotationGenerator->generateCotations($crypto);
        }

        return Command::SUCCESS;

    }
}
