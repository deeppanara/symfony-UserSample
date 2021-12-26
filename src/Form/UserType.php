<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserAddress;
use App\Entity\UserProfile;
use App\Repository\UserAddressRepository;
use App\Repository\UserProfileRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    private $userProfileRepository;
    private $userAddressRepository;
    private $entityManager;

    public function __construct(UserProfileRepository $userProfileRepository, UserAddressRepository $userAddressRepository, EntityManagerInterface $entityManager)
    {
        $this->userProfileRepository = $userProfileRepository;
        $this->userAddressRepository = $userAddressRepository;
        $this->entityManager = $entityManager;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $object = $builder->getForm()->getData();
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter email address.'
                    ])
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new Email([
                        'message' => "Please enter valid contact email address."
                    ])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Role',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'data' => $object->getRoles()[0],
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER'
                ],
                'mapped' => false
            ])
        ;
        if ($object->getId() == null) {
            $builder->add('password',  PasswordType::class, [
                'invalid_message' => 'The password fields must match.',
                'required' => true,
            ]);
        }

        $userProfile = $this->userProfileRepository->findOneBy(['user_id' => $object->getId()]);
        if (!$userProfile) {
            $userProfile = new UserProfile();
        }

        $builder->add('UserProfile', UserProfileType::class, [
            'label'=> "UserProfile",
            'mapped' => false,
            'required' => false,
            'data' => $userProfile,
        ]);


//        $userAddress = $this->userAddressRepository->findBy(['user' => $object->getId()]);
//        if (!$userAddress && empty($userAddress)) {
//            $userAddress[] = new UserAddress();
//        }
//
//        $builder->add('UserAddress', CollectionType::class, [
//            'label'=> "UserAddress",
//            'mapped' => false,
//            'required' => false,
//            'data' => $userAddress,
//        ]);

        $builder
            ->addEventListener(FormEvents::POST_SUBMIT, array($this, 'postSubmit'));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * Post submit.
     *
     * @param FormEvent $event
     *
     * @throws NotFoundHttpException
     */
    public function postSubmit(FormEvent $event)
    {
        $form   = $event->getForm();
        $object = $event->getData();


        if ($form->isValid()) {
            if ($form->has('UserProfile') && $form->get('UserProfile')->getData()) {
                $object->setUserProfile($form->get('UserProfile')->getData());
            }
            if ($form->has('roles') && $form->get('roles')->getData()) {
                $object->setRoles($form->get('roles')->getData());
            }

//            if ($form->has('UserAddress') && $form->get('UserAddress')->getData()) {
//                $userAddress = $this->userAddressRepository->findBy(['user' => $object->getId()]);
//                foreach ($userAddress as $address) { $this->entityManager->remove($address); }
//
//                foreach ($form->get('UserAddress')->getData() as $userAddress) {
//                    $object->addUserAddress((new UserAddress())->setAddress($userAddress));
//                }
//            }
        }

    }
}
