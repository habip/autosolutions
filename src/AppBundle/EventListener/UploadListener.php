<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Oneup\UploaderBundle\Event\PostUploadEvent;
use Oneup\UploaderBundle\Event\PreUploadEvent;
use Oneup\UploaderBundle\UploadEvents;

use AppBundle\Entity\Image;
use AppBundle\Entity\Thumb;
use AppBundle\Utils\ThumbManager;
use AppBundle\Utils\PathResolver;

class UploadListener implements EventSubscriberInterface
{
    private $doctrine;
    private $logger;
    private $tokenStorage;
    private $authChecker;
    private $thumbManager;
    private $pathResolver;

    public function __construct($doctrine, $logger, TokenStorageInterface $tokenStorage,
            AuthorizationCheckerInterface $authChecker, ThumbManager $thumbManager, PathResolver $pathResolver)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
        $this->thumbManager = $thumbManager;
        $this->pathResolver = $pathResolver;
    }

    public static function getSubscribedEvents()
    {
        return array(
            /*UploadEvents::PRE_UPLOAD => 'onPreUpload',*/
            UploadEvents::POST_UPLOAD => 'onUpload'
        );
    }

    public function onUpload(PostUploadEvent $event)
    {
        if (!$this->authChecker->isGranted('ROLE_USER')) {
            throw new AccessDeniedException();
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $file = $event->getFile();
        $type = $event->getRequest()->request->get('type');


        $em = $this->doctrine->getManager();

        $image = new Image();
        $image->setUser($user);

        $image->setPath($this->pathResolver->getPath($file));
        $image->setUrl($this->pathResolver->getUrl($file));

        $thumb = $this->thumbManager->createThumb(
            $image,
            $event->getRequest()->request->get('thumb_width', 400),
            $event->getRequest()->request->get('thumb_height', 300),
            $event->getRequest()->request->get('thumb_crop', 'false') == 'true');

        if (!($thumb->getWidth() == 100 && $thumb->getHeight() == 100 && $thumb->getIsCropped())) {
            $thumb100x100 = $this->thumbManager->createThumb($image, 100, 100, true);
        } else {
            $thumb100x100 = null;
        }

        $em->getConnection()->beginTransaction();

        try {
            $em->persist($image);
            $em->persist($thumb);
            if ($thumb100x100) {
                $em->persist($thumb100x100);
            }

            $em->getConnection()->commit();
            $em->flush();

            $response = $event->getResponse();

            $fileInfo = array(
                'id' => $image->getId(),
                'url' => $image->getUrl(),
                'thumbnailUrl' => $thumb->getUrl(),
                'name' => $file->getBasename(),
                'type' => $file->getMimeType(),
                'size' => $file->getSize()
            );

            $response['files'] = array(
                $fileInfo
            );


        } catch(Exception $ex) {
            $em->getConnection()->rollback();
            throw $ex;
        }
    }

    /*
    public function onPreUpload(PreUploadEvent $event)
    {
        $this->logger->debug("pre upload event fired, file: " . $event->getFile());
    }
    */
}