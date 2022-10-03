<?php

namespace App\Controller;

use App\Entity\Person;
use App\Events\AddPersonEvent;
use App\Form\PersonType;
use App\Services\PDFService;
use App\Services\UploadService;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route('/person'),
    IsGranted('ROLE_USER')
]
class PersonController extends AbstractController
{
    public function __construct(private EventDispatcherInterface $evtDispatcher){}

    #[Route('/list', name: 'app_person')]
    public function list(ManagerRegistry $mr): Response
    {
        $repo = $mr->getRepository(Person::class);
        $persons = $repo->findAll();

        return $this->render('person/index.html.twig', [
            'persons' => $persons
        ]);
    }

    #[Route('/add', name: 'app_person_add')]
    public function add(ManagerRegistry $mr, Request $req): Response
    {
        $person = new Person();
        //$person is the image of the form
        $form = $this->createForm(PersonType::class, $person);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        //form treats request
        $form->handleRequest($req);

        if($form->isSubmitted()){
            //ObjectManager
            $em = $mr->getManager();
            $em->persist($person);
            $em->flush();

            $this->addFlash('info', 'A new person has been added via the Form!');

            return $this->redirectToRoute('app_person');
        } else {
            return $this->render('form.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/get/{id<\d+>}', name: 'app_person_get')]
    public function getById(Person $person = null): Response
    {
        if(!$person){
            $this->addFlash('error', "This person doesn't exist");
            return $this->redirectToRoute('app_person');
        }

        return $this->render('person/show-one.html.twig', [
            'person' => $person
        ]);
    }

    #[Route('/filter/{page?1}/{nb?10}', name: 'app_person_filter')]
    public function filter(ManagerRegistry $mr, int $page, int $nb): Response
    {
        $repo = $mr->getRepository(Person::class);
        $persons = $repo->findBy([], [], $nb, ($page * $nb) - $nb);
        $nbPersons = $repo->count([]);
        $nbPages = ceil($nbPersons / $nb);

        if(!$persons){
            $this->addFlash('error', "No entry found.");
        }

        return $this->render('person/pagination.html.twig', [
            'persons' => $persons,
            'actualPage' => $page,
            'nbPerPage' => $nb,
            'nbPages' => $nbPages
        ]);
    }

    #[
        Route('/delete/{id<\d+>}', name: 'app_person_delete'),
        IsGranted('ROLE_ADMIN')
    ]
    public function remove(ManagerRegistry $mr, Person $person = null) : RedirectResponse {
        if($person){
            $em = $mr->getManager();
            $em->remove($person);

            $em->flush();

            $this->addFlash('info', 'A person has been deleted.');
        } else {
            $this->addFlash('error', 'No person has been deleted.');
        }

        return $this->redirectToRoute('app_person_filter');
    }

    #[Route('/update/{id<\d+>}/{firstname}', name: 'app_person_update')]
    public function update(Person $person = null, ManagerRegistry $mr, int $id, string $firstname): Response {
        if ($person){
            $person->setFirstname($firstname);
            $em = $mr->getManager();
            //persist makes a dif between add and update, thanks to the id
            $em->persist($person);

            $em->flush();
            $this->addFlash('info', 'Person updated.');
        } else {
            $this->addFlash('error', 'Person not updated.');
        }

        return $this->redirectToRoute('app_person_filter');
    }

    #[Route('/filterbyages/{minAge}/{maxAge}', name: 'app_person_filter_age')]
    public function filterByAgeInterval(ManagerRegistry $mr, int $minAge, int $maxAge): Response {

        $em = $mr->getRepository(Person::class);
        $persons = $em->filterByAgeInterval($minAge, $maxAge);

        return $this->render('person/index.html.twig', [
            'persons' => $persons
        ]);
    }

    #[Route('/statsbyages', name: 'app_person_stats_age')]
    public function statsByAgeInterval(ManagerRegistry $mr, Request $request): Response {

        $minAge = $request->query->get('minage');
        $maxAge = $request->query->get('maxage');

        if(!$maxAge && !$minAge) {
            $minAge = 18;
            $maxAge = 25;
        }

        $em = $mr->getRepository(Person::class);
        $stats = $em->statsOnAgeInterval($minAge, $maxAge);


        return $this->render('person/stats.html.twig', [
            'avgAge' => $stats[0]['avgAge'],
            'nbPersons' => $stats[0]['nbPersons'],
            'minAge' => $minAge,
            'maxAge' => $maxAge
        ]);
    }

    #[Route('/updatef/{id?0}', name: 'app_person_updatef')]
    public function updateF(Person $person = null, ManagerRegistry $mr, Request $req, UploadService $us): Response {
        $isNew = false;
        $this->denyAccessUnlessGranted('ROLE_USER');

        if(!$person){
            $isNew = true;
            $person = new Person();
            $person->setCreatedBy($this->getUser());
        }

        $form = $this->createForm(PersonType::class, $person);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $em = $mr->getManager();

            $pic = $form->get('pic')->getData();

            if ($pic) {
                $person->setImage($us->uploadImage($pic, $this->getParameter('pics_directory')));
            }

            $em->persist($person);
            $em->flush();

            if($isNew){
                $evt = new AddPersonEvent($person);
                $this->evtDispatcher->dispatch($evt, AddPersonEvent::ADD_PERSON_EVENT);
            }

            return $this->redirectToRoute('app_person_get', [
                'id' => $person->getId()
            ]);
        } else {
            return $this->render('person/form.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }

    #[Route('/pdf/{id}', name: 'app_person_pdf')]
    public function handlePdf(Person $person = null, PDFService $pdfs){

        $html =  $this->render('person/base-show-one.html.twig', [
            'person' => $person
        ]);
        $pdfs->showPdf($html);

        return new Response(
            headers:['content-type' => 'application/pdf']
        );
    }

    private function consoleLog($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);

        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }
}