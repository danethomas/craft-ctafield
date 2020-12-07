<?php

namespace statikbe\cta\models;

use craft\base\ElementInterface;
use craft\helpers\Html;
use statikbe\cta\fields\CTAField;
use yii\base\Model;

/**
 * Class InputLinkType
 * @package cta\models
 */
class InputLinkType extends Model implements LinkTypeInterface
{
    /**
     * @var string
     */
    public $displayName;

    /**
     * @var string
     */
    public $displayGroup = 'Common';

    /**
     * @var string
     */
    public $inputType;

    /**
     * @var string
     */
    public $placeholder;


    /**
     * ElementLinkType constructor.
     * @param string|array $displayName
     * @param array $options
     */
    public function __construct($displayName, array $options = []) {
        if (is_array($displayName)) {
            $options = $displayName;
        } else {
            $options['displayName'] = $displayName;
        }

        parent::__construct($options);
    }

    /**
     * @return array
     */
    public function getDefaultSettings(): array {
        return [
            'disableValidation' => false,
        ];
    }

    /**
     * @return string
     */
    public function getDisplayName(): string {
        return \Craft::t('cta', $this->displayName);
    }

    /**
     * @return string
     */
    public function getDisplayGroup(): string {
        return \Craft::t('cta', $this->displayGroup);
    }

    /**
     * @param Link $link
     * @return ElementInterface|null
     */
    public function getElement(CTA $link) {
        return null;
    }

    /**
     * @param string $linkTypeName
     * @param LinkField $field
     * @param Link $value
     * @param ElementInterface $element
     * @return string
     */
    public function getInputHtml(string $linkTypeName, CTAField $field, CTA $value, ElementInterface $element): string {
        $settings   = $field->getLinkTypeSettings($linkTypeName, $this);
        $isSelected = $value->type === $linkTypeName;
        $value      = $isSelected ? $value->value : '';

        $textFieldOptions = [
            'id'    => $field->handle . '-' . $linkTypeName,
            'name'  => $field->handle . '[' . $linkTypeName . ']',
            'value' => $value,
        ];

        if (isset($this->inputType) && !$settings['disableValidation']) {
            $textFieldOptions['type'] = $this->inputType;
        }

        if (isset($this->placeholder)) {
            $textFieldOptions['placeholder'] = \Craft::t('cta', $this->placeholder);
        }

        if($this->inputType === 'url') {
            $textFieldOptions['placeholder'] = 'https://';
        }

        if($this->inputType === 'email') {
            $textFieldOptions['placeholder'] = 'test@example.com';
        }

        try {
            return \Craft::$app->view->renderTemplate('cta/_input-input', [
                'isSelected'       => $isSelected,
                'linkTypeName'     => $linkTypeName,
                'textFieldOptions' => $textFieldOptions,
            ]);
        } catch (\Throwable $exception) {
            return Html::tag('p', \Craft::t(
                'cta',
                'Error: Could not render the template for the field `{name}`.',
                [ 'name' => $this->getDisplayName() ]
            ));
        }
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function getLinkValue($value) {
        return is_string($value) ? $value : '';
    }

    /**
     * @param string $linkTypeName
     * @param LinkField $field
     * @return string
     */
    public function getSettingsHtml(string $linkTypeName, CTAField $field): string {
        try {
            return \Craft::$app->view->renderTemplate('cta/_settings-input', [
                'settings'     => $field->getLinkTypeSettings($linkTypeName, $this),
                'elementName'  => $this->getDisplayName(),
                'linkTypeName' => $linkTypeName,
            ]);
        } catch (\Throwable $exception) {
            return Html::tag('p', \Craft::t(
                'cta',
                'Error: Could not render the template for the field `{name}`.',
                [ 'name' => $this->getDisplayName() ]
            ));
        }
    }

    /**
     * @param Link $link
     * @return null|string
     */
    public function getText(CTA $link) {
        return null;
    }

    /**
     * @param Link $link
     * @return null|string
     */
    public function getUrl(CTA $link) {
        if ($this->isEmpty($link)) {
            return null;
        }

        switch ($this->inputType) {
            case('email'):
                return 'mailto:' . $link->value;
            case('tel'):
                return 'tel:' . $link->value;
            default:
                return $link->value;
        }
    }

    /**
     * @param Link $link
     * @return bool
     */
    public function hasElement(CTA $link): bool {
        return false;
    }

    /**
     * @param Link $link
     * @return bool
     */
    public function isEmpty(CTA $link): bool {
        if (is_string($link->value)) {
            return trim($link->value) === '';
        }

        return true;
    }

    /**
     * @param LinkField $field
     * @param Link $link
     * @return array|null
     */
    public function validateValue(CTAField $field, CTA  $link) {
        if ($this->isEmpty($link)) {
            return null;
        }

        $settings = $field->getLinkTypeSettings($link->type, $this);
        if ($settings['disableValidation']) {
            return null;
        }

        $value = $link->value;

        switch ($this->inputType) {
            case('email'):
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return [\Craft::t('cta', 'Please enter a valid email address.'), []];
                }
                break;

            case('tel'):
                $regexp = '/^[0-9+\(\)#\.\s\/ext-]+$/';
                if (!filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $regexp)))) {
                    return [\Craft::t('cta', 'Please enter a valid phone number.'), []];
                }
                break;

            case('url'):
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    return [\Craft::t('cta', 'Please enter a valid url.'), []];
                }
                break;
        }

        return null;
    }
}
