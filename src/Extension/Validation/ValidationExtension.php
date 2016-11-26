<?php namespace Barryvdh\Form\Extension\Validation;


use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidationExtension extends AbstractTypeExtension
{
    /** @var ValidationFactory  */
    protected $validator;

    public function __construct(ValidationFactory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('rules'));
        $resolver->setDefault('rules', array());

        // Split rule into array
        $rulesNormalizer = function (Options $options, $constraints) use ($resolver) {

            if (is_string($constraints)) {
                $rules = explode('|', $constraints);
            } elseif (is_object($constraints)) {
                $rules = [$constraints];
            } else {
                $rules = $constraints;
            }

            return $rules;
        };

        $resolver->setNormalizer('rules', $rulesNormalizer);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ValidationListener($this->validator));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return FormType::class;
    }
}
