<?php

namespace tinocachePlugin\Model\Api\CPanel;

class UsersDomains extends Cli
{

    private $userdomains;

    /**
     * List all domains
     *
     * @return array
     */
    public function getAll()
    {
        $this->userdomains = $this->execute('cat /etc/userdomains');

        return $this;
    }

    public function get($username)
    {
        $this->userdomains = $this->execute('cat /etc/userdomains | grep "'.$username.'"');

        return $this;
    }

    public function prepareDomains()
    {
        $userdomains = explode("\n", $this->userdomains);

        $preparedDomains = [];

        foreach ($userdomains as $domain)
        {
            $domain                        = explode(":", str_replace(" ", "", $domain));
            $preparedDomains[$domain[1]][] = (object)['domainName' => $domain[0]];
        }

        // Get registered users
        $customers = new \tinocachePlugin\Model\Db\Table\Customer();
        $customer  = new \tinocachePlugin\Model\Api\ComodoApi\Functions\Engine\Customer();

        foreach ($customers->all() as $customerDb)
        {
            if (empty($customerDb->camid) || !isset($preparedDomains[$customerDb->cpaneluser]))
            {
                continue;
            }

            // Get protected domains
            $customer->camId    = $customerDb->camid;
            $customer->username = $customerDb->cwatchuser;
            $customer->password = $customerDb->password;

            $customerInfo = $customer->setHeader()->getCustomerInfo();

            $tempDomains = [];

            foreach ($customerInfo->licenses as $license)
            {
                // Check if license expired
                if ($license->status !== 1)
                {
                    continue;
                }

                // Check if there are domains under license
                if (empty($license->domains))
                {
                    continue;
                }

                // Set info of protected domain
                foreach ($license->domains as $domain)
                {
                    $tempDomains[$domain->name] = $domain;
                }
            }

            foreach ($preparedDomains[$customerDb->cpaneluser] as $domain)
            {
                if (isset($tempDomains[$domain->domainName]))
                {
                    $domain->info = $tempDomains[$domain->domainName];
                }
            }
        }
//echo json_encode($preparedDomains); die;
        return $preparedDomains;
    }
}
