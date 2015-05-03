<?php
namespace Respect\Validation\Rules;

use Respect\Validation\Validator as v;

class DomainTest extends \PHPUnit_Framework_TestCase
{
    protected $validator;

    protected function setUp()
    {
        $this->validator = new Domain;
    }

    /**
     * @dataProvider provideValidDomainWithValidTld
     */
    public function testValidDomainWithValidTld($domain)
    {
        $this->validator->tldCheck(true);

        $this->assertTrue(
            $this->validator->__invoke($domain),
            'Validation with Callable interface.'
        );
        $this->assertTrue(
            $this->validator->assert($domain),
            'Validation with `assert` API.'
        );
        $this->assertTrue(
            $this->validator->check($domain),
            'Validation with `check` API.'
        );
    }

    public function provideValidDomainWithValidTld()
    {
        return array(
            'simple domain' => array('example.com'),
            'domain with punycode' => array('xn--bcher-kva.ch'),
            'subdomain on punycode domain' => array('mail.xn--bcher-kva.ch'),
            'domain with hypjen' => array('example-hyphen.com'),
            'one letter domain' => array('t.co'),
            'subdomain with one letter domain' => array('dev.t.co'),
            'org tld' => array('apache.org'),
            'google' => array('google.com'),
            'io tld' => array('git.io'),
            'domain with many dashes' => array('d-o-m-a-i-n.com'),
            'case insentive domain' => array('GoOgLe.CoM'),
            'two letter domain' => array('uk.gov'),
            'domain with double dashes' => array('something--strange.uk'),
            'domain with multiple dashes' => array('test---domain.com'),
        );
    }

    /**
     * @dataProvider provideValidDomainWithInvalidTld
     */
    public function testValidDomainsWithInvalidTldsWhenTldCheckIsEnabledFails($domain)
    {
        $this->validator->tldCheck(true);

        $this->assertFalse(
            $this->validator->validate($domain),
            'Validation with `validate` API.'
        );
    }

    /**
     * @dataProvider provideValidDomainWithInvalidTld
     */
    public function testValidDomainWithoutTldCheck($domain)
    {
        $this->validator->tldCheck(false);

        $this->assertTrue(
            $this->validator->__invoke($domain),
            'Validation with Callable interface.'
        );
        $this->assertTrue(
            $this->validator->assert($domain),
            'Validation with `assert` API.'
        );
        $this->assertTrue(
            $this->validator->check($domain),
            'Validation with `check` API.'
        );
    }

    public function provideValidDomainWithInvalidTld()
    {
        return array(
            'domain with dash' => array('my-app.local'),
            'simple domain' => array('domain.local'),
            'subdmain' => array('sub.domain.local'),
            'domain starting with number' => array('1domain.dev'),
            'hostname should be valid' => array('localhost'),
            'hostname with dashes' => array('machine-name'),
            'hostname with numbers' => array('web01'),
        );
    }
    /**
     * @dataProvider provideInvalidDomains
     * @expectedException Respect\Validation\Exceptions\ValidationException
     */
    public function testNotDomain($input, $tldcheck=true)
    {
        $this->validator->tldCheck($tldcheck);
        $this->assertFalse($this->validator->check($input));
    }

    /**
     * @dataProvider provideInvalidDomains
     * @expectedException Respect\Validation\Exceptions\DomainException
     */
    public function testNotDomainCheck($input, $tldcheck=true)
    {
        $this->validator->tldCheck($tldcheck);
        $this->assertFalse($this->validator->assert($input));
    }

    public function provideInvalidDomains()
    {
        return array(
            'null is not a valid domain' => array(null),
            'empty string is not a valid domain' => array(''),
            array('example--invalid.com'),
            'domain starting with dash is not valid' => array('-example-invalid.com'),
            'TLD with dash is not valid' => array('example.invalid.-com'),
            array('xn--bcher--kva.ch'),
            'Invalid IP address is not a valid domain' => array('1.2.3.256'),
            'Valid IP address is not a valiod domain' => array('1.2.3.4'),
            'domain starting with a dash is invalid' => array('-not.com'),
            'domain with spaces is invalid' => array('go google.com'),
            'domain starting with space is invalid' => array(' google.com'),
            'domain ending with multiple dashes is invalid' => array('google---.com'),
            'URL is not a valid domain' => array('http://google.com'),
            'TLD is not a valid domain' => array('.com')
        );
    }

    /**
     * @dataProvider provideValidDomainWithValidTld
     */
    public function testBuilder($validDomain, $checkTLD=true)
    {
        $this->assertTrue(
            v::domain($checkTLD)->validate($validDomain),
            sprintf('Domain "%s" should be valid. (Check TLD: %s)', $validDomain, var_export($checkTLD, true))
        );
    }
}

