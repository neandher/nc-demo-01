<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Customer;
use App\Event\FlashBagEvents;
use App\Form\CustomerType;
use App\Util\FlashBag;
use App\Util\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CustomerController
 * @package App\Controller\Admin
 *
 * @Route("/customer", name="admin_customer_")
 */
class CustomerController extends BaseController
{
    /**
     * @var Pagination
     */
    private $pagination;
    /**
     * @var FlashBag
     */
    private $flashBag;

    /**
     * CustomerController constructor.
     * @param Pagination $pagination
     * @param FlashBag $flashBag
     */
    public function __construct(Pagination $pagination, FlashBag $flashBag)
    {
        $this->pagination = $pagination;
        $this->flashBag = $flashBag;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $pagination = $this->pagination->handle($request, Customer::class);

        $customers = $this->getDoctrine()->getRepository(Customer::class)->findLatest($pagination);

        $deleteForms = [];
        foreach ($customers as $customer) {
            $deleteForms[$customer->getId()] = $this->createDeleteForm($customer)->createView();
        }

        return $this->render('admin/customer/index.html.twig', [
            'customers' => $customers,
            'pagination' => $pagination,
            'delete_forms' => $deleteForms,
        ]);
    }

    /**
     * @Route("/new", name="new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $pagination = $this->pagination->handle($request, Customer::class);

        $customer = new Customer();

        $form = $this->createForm(CustomerType::class, $customer);
        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_INSERTED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_customer_new',
                'admin_customer_edit',
                ['id' => $customer->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_customer_index', $pagination->getRouteParams());
        }

        return $this->render('admin/customer/new.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/edit", requirements={"id" : "\d+"}, name="edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function editAction(Customer $customer, Request $request)
    {
        $pagination = $this->pagination->handle($request, Customer::class);

        $form = $this->createForm(CustomerType::class, $customer);

        $this->addDefaultSubmitButtons($form);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_UPDATED
            );

            $handleSubmitButtons = $this->handleSubmitButtons(
                $form,
                'admin_customer_new',
                'admin_customer_edit',
                ['id' => $customer->getId()],
                $pagination->getRouteParams()
            );

            return $handleSubmitButtons ? $handleSubmitButtons : $this->redirectToRoute('admin_customer_index');
        }

        return $this->render('admin/customer/edit.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" : "\d+"}, name="delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function deletAction(Request $request, Customer $customer)
    {
        $pagination = $this->pagination->handle($request, Customer::class);

        $form = $this->createDeleteForm($customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();

            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_SUCCESS,
                FlashBagEvents::MESSAGE_SUCCESS_DELETED
            );
        } else {
            $this->flashBag->newMessage(
                FlashBagEvents::MESSAGE_TYPE_ERROR,
                FlashBagEvents::MESSAGE_ERROR_DELETED
            );
        }

        return $this->redirectToRoute('admin_customer_index', $pagination->getRouteParams());
    }

    /**
     * @param Customer $customer
     * @return FormInterface
     */
    private function createDeleteForm(Customer $customer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_customer_delete', ['id' => $customer->getId()]))
            ->setMethod('DELETE')
            ->setData($customer)
            ->getForm();
    }
}
