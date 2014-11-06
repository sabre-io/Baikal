<?php

namespace Baikal\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Validator\Constraints\NotBlank;

use Baikal\AdminBundle\Entity\Settings;
use Baikal\DavServicesBundle\BaikalDavServicesBundle;

class SettingsController extends Controller
{
    public function indexAction(Request $request)
    {
        # Displaying the edition form
        $formBuilder = $this->getFormBase();

        $config = $this->get('config.main');

        $form = $formBuilder->setData(array(
            'server_timezone' => $config->hasServer_timezone() ? $config->getServer_timezone() : 'Europe/Paris',
            'enable_caldav' => $config->hasEnable_caldav() ? $config->getEnable_caldav() : true,
            'enable_carddav' => $config->hasEnable_carddav() ? $config->getEnable_carddav() : true,
            'enable_versioncheck' => $config->hasEnable_versioncheck() ? $config->getEnable_versioncheck() : true,
        ))->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {

            $data = $form->getData();

            # Persisting settings
            $config->setServer_timezone($data['server_timezone']);
            $config->setEnable_caldav($data['enable_caldav']);
            $config->setEnable_carddav($data['enable_carddav']);
            $config->setEnable_versioncheck($data['enable_versioncheck']);

            $this->get('session')->getFlashBag()->add('notice', '<i class="fa fa-cogs"></i> Settings have been updated.');
            return $this->redirect($this->generateUrl('baikal_admin_settings'));
        }

        return $this->render('BaikalAdminBundle:Settings:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    protected function getFormBase() {

        return $this->createFormBuilder()
            ->add('server_timezone', 'timezone', array(
                'label' => 'Server time zone',
                'multiple' => FALSE,
                'expanded' => FALSE,
                'constraints' => array(
                    new NotBlank(),
                )
            ))
            ->add('enable_caldav', 'checkbox', array(
                'label' => 'CalDAV - Calendar service',
                'required' => FALSE,
            ))
            ->add('enable_carddav', 'checkbox', array(
                'label' => 'CardDAV - Contact service',
                'required' => FALSE,
            ))
            ->add('enable_versioncheck', 'checkbox', array(
                'label' => 'Check for updates on the admin dashboard',
                'required' => FALSE,
            ));
    }
}
