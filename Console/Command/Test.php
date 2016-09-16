<?php 

/**
 * A Magento 2 module named Experius/Postcode
 * Copyright (C) 2016 Experius
 * 
 * This file included in Experius/Postcode is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

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
		
		
		print_r($this->postcodeHelper->getJsinit(false));
		
        
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