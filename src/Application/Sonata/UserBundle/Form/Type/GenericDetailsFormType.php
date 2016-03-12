<?php

/*
 * User Bundle
 * This file is part of the BardisCMS.
 *
 * (c) George Bardis <george@bardis.info>
 *
 */

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use Application\Sonata\UserBundle\Entity\User;

class GenericDetailsFormType extends AbstractType {

	private $class;
	private $container;

    /**
     * @param string $class The User class name
     * @param Container $container
     */
	public function __construct($class, $container) {
		$this->class = $class;
		$this->container = $container;
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {

        $now = new \DateTime();
		$user = $this->container->get('security.context')->getToken()->getUser();

		$defaults = array(
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'title' => $user->getTitle(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'gender' => $user->getGender(),
            'dateOfBirth' => $user->getDateOfBirth()
		);

        // Adding custom extra user fields for Generic Details (with Profile Details included) Form
		$builder
            ->add('email', EmailType::class, array(
                'label' => 'form.email',
                'translation_domain' => 'SonataUserBundle',
                'data' => $defaults['email'],
                "attr" => [
                    'placeholder' => "user@domain.com",
                    'minlength' => 2,
                    'maxlength' => 255
                ],
                'required' => true
            ))
            ->add('username', TextType::class, array(
                'label' => 'form.username',
                'translation_domain' => 'SonataUserBundle',
                'data' => $defaults['username'],
                "attr" => [
                    'placeholder' => "username",
                    'minlength' => 6,
                    'maxlength' => 20
                ],
                'required' => true,
                'read_only' => true
            ))
            /*
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'options' => array(
                    'translation_domain' => 'SonataUserBundle',
                    'error_bubbling' => false,
                ),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch'
            ))
            */
			->add('title', ChoiceType::class, array(
                'choices' => array(
                    User::TITLE_MR      => 'mr',
                    User::TITLE_MS      => 'ms',
                    User::TITLE_MRS     => 'mrs',
                    User::TITLE_MISS    => 'miss',
                    User::TITLE_DR      => 'dr',
                    User::TITLE_PROF    => 'prof',
                ),
                'label' => 'form.title',
                'translation_domain' => 'SonataUserBundle',
                'data' => $defaults['title'],
                'required' => true,
                'expanded' => false,
                'multiple' => false
            ))
			->add('firstname', TextType::class, array(
                'label' => 'form.firstname',
                'translation_domain' => 'SonataUserBundle',
                'data' => $defaults['firstname'],
                "attr" => [
                    'placeholder' => "Name",
                    'minlength' => 2,
                    'maxlength' => 50
                ],
                'required' => false
            ))
			->add('lastname', TextType::class, array(
                'label' => 'form.lastname',
                'translation_domain' => 'SonataUserBundle',
                'data' => $defaults['lastname'],
                "attr" => [
                    'placeholder' => "Surname",
                    'minlength' => 2,
                    'maxlength' => 50
                ],
                'required' => false
            ))
			->add('gender', ChoiceType::class, array(
                'choices' => array(
                    User::GENDER_UNKNOWN   => 'u',
                    User::GENDER_FEMALE    => 'f',
                    User::GENDER_MALE      => 'm'
                ),
                'label' => 'form.gender',
                'translation_domain' => 'SonataUserBundle',
                'data' => $defaults['gender'],
                'required' => true,
                'expanded' => false,
                'multiple' => false
            ))
            ->add('dateOfBirth', DateType::class, array(
                'format' => 'dd-MM-yyyy',
                'widget' => 'single_text',
                'label' => 'form.dateOfBirth',
                'translation_domain' => 'SonataUserBundle',
                'data' => $defaults['dateOfBirth'],
                'placeholder' => 'dd-mm-yyyy',
                'html5' => true,
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'datepickerField',
                    'data-date-language' => $this->container->get('translator')->getLocale(),
                    'data-picker-position' => 'bottom-right',
                    'data-date-format' => 'dd-mm-yyyy',
                    'placeholder' => "dd-mm-yyyy",
                    'minlength' => 10,
                    'maxlength' => 10
                ],
                'invalid_message' => 'dateOfBirth.isNotDate',
                'required' => false
            ))
		;
	}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention' => 'profile_generic_details_edit',
            'error_mapping' => array(
                'validDateOfBirth' => 'dateOfBirth',
                'safePassword' => 'plainPassword',
            )
        ));
    }

    public function getName() {
        return $this->getBlockPrefix();
    }

    // Define the name of the form to call it for rendering
    public function getBlockPrefix() {
        return 'sonata_user_generic_details';
    }

    public function getExtendedType()
    {
        return method_exists(AbstractType::class, 'getBlockPrefix') ? FormType::class : 'form';
    }

}
