<?php

namespace Sebastienheyd\Systempay;

class Systempay
{
    protected $key;
    protected $algo;
    protected $params = [];
    const URL = 'https://paiement.systempay.fr/vads-payment/';

    /**
     * Systempay constructor.
     *
     * @param string $config
     */
    public function __construct(string $config = 'default')
    {
        return $this->config($config);
    }

    /**
     * @param string $config
     *
     * @return self
     */
    public function config(string $configName = 'default'): self
    {
        if (!$config = config("systempay.{$configName}")) {
            throw new \UnexpectedValueException(sprintf('No configuration "%s" found', $configName));
        }

        $this->key = $config['key'];
        $this->algo = $config['algo'] ?? 'sha256';

        if (!isset($config['params'])) {
            $config['params'] = [];
        }

        $this->set($config['params'] + [
            'amount'         => 0,
            'page_action'    => 'PAYMENT',
            'action_mode'    => 'INTERACTIVE',
            'payment_config' => 'SINGLE',
            'version'        => 'V2',
            'currency'       => '978',
            'site_id'        => $config['site_id'],
            'ctx_mode'       => $config['env'],
        ]);

        return $this;
    }

    /**
     * Set parameter(s). You can do a massive assignement by passing an associative array as $param.
     *
     * @param string|array $param
     * @param string       $value
     *
     * @return self
     *
     * @see https://paiement.systempay.fr/doc/fr-FR/form-payment/quick-start-guide/envoyer-un-formulaire-de-paiement-en-post.html
     */
    public function set($param, $value = null): self
    {
        if (is_string($param)) {
            $param = [$param => $value];
        }

        foreach ($param as $k => $v) {
            if ($v == null) {
                continue;
            }

            if (preg_match('#^vads_#', $k)) {
                $k = preg_replace('#^vads_#', '', $k);
            }

            if ($k === 'amount') {
                $v = $v * 100;
            }

            $this->params[$k] = $v;
        }

        ksort($this->params);

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return string
     */
    private function getSignature(): string
    {
        $str = implode('+', $this->params).'+'.$this->key;

        if ($this->algo === 'sha256') {
            if (!in_array('sha256', hash_hmac_algos())) {
                throw new \Exception('Algorithm SHA-256 is not available on this server');
            }

            return base64_encode(hash_hmac('sha256', $str, $this->key, true));
        }

        return sha1($str);
    }

    /**
     * Render form and input tags.
     *
     * @param string $button
     *
     * @throws \Exception
     *
     * @return string
     */
    public function render(string $button = '<button type="submit">Pay</button>'): string
    {
        $html = sprintf('<form method="post" action="%s" accept-charset="UTF-8">', self::URL);

        if (!isset($this->params['trans_date'])) {
            $this->set('trans_date', gmdate('YmdHis'));
        }

        foreach ($this->params as $key => $value) {
            $html .= sprintf('<input type="hidden" name="vads_%s" value="%s">', $key, $value);
        }

        $html .= sprintf('<input type="hidden" name="signature" value="%s">', $this->getSignature());
        $html .= $button;
        $html .= '</form>';

        return $html;
    }
}
