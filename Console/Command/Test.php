<?php 


namespace Experius\Postcode\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface; 
 
class Test extends Command {

	const NAME_ARGUMENT = "vat";
	const NAME_OPTION = "validate";
    
    protected $postcodeHelper;
    
    public function __construct(
        \Experius\Postcode\Helper\Data $postcodeHelper
    ){
       $this->postcodeHelper = $postcodeHelper;
       
       parent::__construct('test');
    }

	protected function execute(
		InputInterface $input,
		OutputInterface $output
	){
		$vat = $input->getArgument(self::NAME_ARGUMENT);
        $option = $input->getOption(self::NAME_OPTION);
        
        $testAddresses = [
			'3512VT'=>'6'
        ];
        
        foreach($testAddresses as $postcode=>$houseNumber){
            $result = $this->postcodeHelper->lookupAddress($postcode,$houseNumber,false); 
            $output->writeln("Result for " . $postcode . ' ' . $houseNumber);
            print_r($result);
			break;
        }
        
	}

	protected function configure(){
		$this->setName("experius_postcode:test");
		$this->setDescription("Test");
		$this->setDefinition([new InputArgument(self::NAME_ARGUMENT,InputArgument::OPTIONAL,"vat"),new InputOption(self::NAME_OPTION,"-a",InputOption::VALUE_NONE,"Option functionality")]);
		parent::configure();
	}
}