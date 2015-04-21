<?php

namespace WsdlToPhp\PackageGenerator\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use WsdlToPhp\PackageGenerator\Generator\Generator;

class GeneratePackageCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setName('wsdltophp:generate:package')
            ->setDescription('Generate package based on options')
            ->addOption('wsdl-urlorpath', 'wsdl-url', InputOption::VALUE_REQUIRED, 'Url or path to WSDL')
            ->addOption('wsdl-destination', 'wsdl-dest', InputOption::VALUE_REQUIRED, 'Path to destination directory, where the package will be generated')
            ->addOption('wsdl-login', 'wsdl-lg', InputOption::VALUE_OPTIONAL, 'Basic authentication login required to access the WSDL url, can be avoided mot of the time')
            ->addOption('wsdl-password', 'wsdl-pass', InputOption::VALUE_OPTIONAL, 'Basic authentication password required to access the WSDL url, can be avoided mot of the time')
            ->addOption('wsdl-proxy-host', 'wsdl-prox-ho', InputOption::VALUE_OPTIONAL, 'Use proxy url')
            ->addOption('wsdl-proxy-port', 'wsdl-prox-po', InputOption::VALUE_OPTIONAL, 'Use proxy port')
            ->addOption('wsdl-proxy-login', 'wsdl-prox-lo', InputOption::VALUE_OPTIONAL, 'Use proxy login')
            ->addOption('wsdl-proxy-password', 'wsdl-prox-pa', InputOption::VALUE_OPTIONAL, 'Use proxy password')
            ->addOption('wsdl-prefix', 'wsdl-prf', InputOption::VALUE_REQUIRED, 'Prepend generated classes')
            ->addOption('wsdl-namespace', 'wsdl-ns', InputOption::VALUE_OPTIONAL, 'Package classes\' namespace')
            ->addOption('wsdl-category', 'wsdl-cat', InputOption::VALUE_OPTIONAL, 'First level directory name generation mode (start, end, cat, none)')
            ->addOption('wsdl-subcategory', 'wsdl-subcat', InputOption::VALUE_OPTIONAL, 'Second level directory name generation mode (start, end, none), disabled if category=cat')
            ->addOption('wsdl-gathermethods', 'wsdl-gath', InputOption::VALUE_OPTIONAL, 'Gather methods based on operation name mode (start, end)')
            ->addOption('wsdl-genwsdlclass', 'wsdl-gwc', InputOption::VALUE_OPTIONAL, 'Enable/Disable main Wsdl class generation, you should always enable this option')
            ->addOption('wsdl-gentutorial', 'wsdl-gt', InputOption::VALUE_OPTIONAL, 'Enable/Disable tutorial file, you should enable this option only on dev')
            ->addOption('wsdl-genautoload', 'wsdl-gauto', InputOption::VALUE_OPTIONAL, 'Enable/Disable autoload file generation, this is useless if you use composer or your own autoloader')
            ->addOption('wsdl-sendarrayparam', 'wsdl-sarpar', InputOption::VALUE_OPTIONAL, 'Enable/Disable usage of an array to send the parameters, can be disabled as it will soon removed')
            ->addOption('wsdl-genericconstants', 'wsdl-cst', InputOption::VALUE_OPTIONAL, 'Enable/Disable usage of generic constants name (ex : ENUM_VALUE_0, ENUM_VALUE_1, etc) or contextual values (ex : VALUE_STRING, VALUE_YES, VALUES_NO, etc)')
            ->addOption('wsdl-reponseasobj', 'wsdl-obj', InputOption::VALUE_OPTIONAL, 'Enable/Disable usage of object to encapsulate Web Service response')
            ->addOption('wsdl-inherits', 'wsdl-inh', InputOption::VALUE_OPTIONAL, 'Astracts struct base name to identify abtract structs, can be avoided as it will be soon removed')
            ->addOption('wsdl-paramsasarray', 'wsdl-pararray', InputOption::VALUE_OPTIONAL, 'Enable/Disable usage of a \'parameters\' parameter in an array to contain request parameters, disabled in most cases');
    }
    
    /**
     * @see \Sdc\AppBundle\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        $this->writeLn(" Start");

        $wsdlUrl            = $this->input->getOption('wsdl-urlorpath');
        $wsdlProxyHost      = $this->input->getOption('wsdl-proxy-host');
        $wsdlProxyPort      = $this->input->getOption('wsdl-proxy-port');
        $wsdlProxyLogin     = $this->input->getOption('wsdl-proxy-login');
        $wsdlProxyPass      = $this->input->getOption('wsdl-proxy-password');
        $wsdlLogin          = $this->input->getOption('wsdl-login');
        $wsdlPassword       = $this->input->getOption('wsdl-password');
        $packageName        = $this->input->getOption('wsdl-prefix');
        $packageDestination = $this->input->getOption('wsdl-destination');
        $wsdlOptions        = array();

        if (!empty($wsdlProxyHost)) {
            $wsdlOptions['proxy_host'] = $wsdlProxyHost;
        }
        if (!empty($wsdlProxyPort)) {
            $wsdlOptions['proxy_port'] = $wsdlProxyPort;
        }
        if (!empty($wsdlProxyLogin)) {
            $wsdlOptions['proxy_login'] = $wsdlProxyLogin;
        }
        if (!empty($wsdlProxyPass)) {
            $wsdlOptions['proxy_password'] = $wsdlProxyPass;
        }
        
        $generator = Generator::instance($wsdlUrl, $wsdlLogin, $wsdlPassword, $wsdlOptions);
        if ($this->canExecute()) {
            $generator->generateClasses($packageName, $packageDestination);
        } else {
            $this->writeLn("  Generation not launched, use --force to force generation");
        }
        
        $this->writeLn(" End");
    }
}
