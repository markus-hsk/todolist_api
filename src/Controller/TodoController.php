<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route; // is need to parse the @Route annotations
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Todo;


class TodoController extends BasicController
{
    /**
     * Fetch the complete list of all available todos
     *
     * @Route("/todos", name="get todo list", methods={"GET"}, defaults={"_format": "json"})
     * @param   Request $request
     * @return  JsonResponse
     * @author  Markus Buscher
     */
    public function getAll(Request $request)
    {
        $search_term = $this->getParam($request, 'searchterm', ['default' => '']);
    	$todos = $this->getTodoList($search_term);
    	
    	$response_data = array();
    	foreach($todos as &$todo)
        {
            $response_data[] = $todo->getPublicDataArray();
        }
    	
    	return $this->successJson($response_data);
    }
    
    
    /**
     * Fetch a specific Todo record by its unique ID
     *
     * @Route("/todos/{id}", name="get a todo by its unique ID", methods={"GET"}, requirements={"id"="\d+"}, defaults={"_format": "json"})
     * @param   int     $id
     * @return  JsonResponse
     * @author  Markus Buscher
     */
    public function getSingle(int $id)
    {
        $todo = $this->getTodoById($id);
    
        $response_data = $todo->getPublicDataArray();
    
        return $this->successJson($response_data);
    }
    
    
    /**
     * Create a new Todo record on the database
     *
     * @Route("/todos", name="create a new todo", methods={"POST"}, defaults={"_format": "json"})
     * @param   Request $request
     * @return  JsonResponse
     * @author  Markus Buscher
     * @throws  \Exception
     */
    public function postNew(Request $request)
    {
        // create the new Record
        $todo = new Todo();
    
        // set the values
        $title = $this->getParam($request, 'title', ['required' => true]);
        $todo->setTitle($title);
        
        $description = $this->getParam($request, 'description', ['default' => null]);
        $todo->setDescription($description);
        
        $owner = $this->getParam($request, 'owner', ['default' => null]);
        $todo->setOwner($owner);
        
        $priority = (int) $this->getParam($request, 'priority', ['default' => 0]);
        $todo->setPriority($priority);
        
        $complete_till_ts = $this->getDatetimeParam($request, 'complete_till_ts',
            ['default' => null]);
        $todo->setCompleteTillTs($complete_till_ts);
        
        $done = (bool) $this->getParam($request, 'done', ['default' => 0]);
        $todo->setDone($done);
        
        $insert_ts = new \DateTime();
        $todo->setInsertTs($insert_ts);
    
        // store the instance in the database
        $this->saveTodo($todo);
        
        $headers = ['Location' => $request->getBaseUrl().'/todos/'.$todo->getId()];
        return $this->successJson([$todo->getPublicDataArray()], JsonResponse::HTTP_CREATED, $headers);
    }
    
    
    /**
     * Update a existing Todo record
     *
     * @Route("/todos/{id}", name="update a specific todo", methods={"PATCH"}, requirements={"id"="\d+"}, defaults={"_format": "json"})
     * @param   Request $request
     * @param   int     $id
     * @return  JsonResponse
     * @author  Markus Buscher
     * @throws  \Exception
     */
    public function patchSingle(Request $request, int $id)
    {
        $todo = $this->getTodoById($id);
    
        // fetch the changes
        $content = $this->getRequestData($request);
        if (array_key_exists('title', $content))
        {
            $todo->setTitle($content['title']);
        }
    
        if (array_key_exists('description', $content))
        {
            $todo->setDescription($content['description']);
        }
    
        if (array_key_exists('owner', $content))
        {
            $todo->setOwner($content['owner']);
        }
    
        if (array_key_exists('priority', $content))
        {
            $todo->setPriority((int) $content['priority']);
        }
    
        if (array_key_exists('complete_till_ts', $content))
        {
            $complete_till_ts = $this->getDatetimeParam($request, 'complete_till_ts');
            $todo->setCompleteTillTs($complete_till_ts);
        }
    
        if (array_key_exists('done', $content))
        {
            $todo->setDone((bool) $content['done']);
        }
    
        // store the changes in the database
        $this->saveTodo($todo);
    
        return $this->successJson([$todo->getPublicDataArray()], 200);
    }
    
    
    /**
     * Delete an existing Todo record from the database
     *
     * @Route("/todos/{id}", name="deletes a specific todo", methods={"DELETE"}, requirements={"id"="\d+"}, defaults={"_format": "json"})
     * @param   int     $id
     * @return  JsonResponse
     * @author  Markus Buscher
     */
    public function deleteSingle(int $id)
    {
        $todo = $this->getTodoById($id);
    
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($todo);
        $entityManager->flush();
    
        return $this->successJson([], 200);
    }
    
    
    
    // ##### internal handling methods #######################################################################
    
    /**
     * Fetches the complete available list of todos
     *
     * @param   string  $searchterm
     * @return  Todo[]
     * @author  Markus Buscher
     */
    protected function getTodoList(string $searchterm = ''): array
    {
        /** @var TodoRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Todo::class);
        
        if(strlen($searchterm) > 0)
        {
            $todos = $repository->findBySearchterm($searchterm);
        }
        else
        {
            $todos = $repository->findAll();
        }
        
        return $todos;
    }
    
    
    /**
     * Fetches a single Todo-instance by the internal ID
     *
     * @param   int $id
     * @return  Todo
     * @author  Markus Buscher
     */
    protected function getTodoById(int $id): Todo
    {
        /** @var Todo $todo */
        $todo = $this->getDoctrine()
                     ->getRepository(Todo::class)
                     ->find($id);
        
        if($todo === null)
        {
            throw new NotFoundHttpException("The Todo #$id could not be found in the database");
        }
        
        return $todo;
    }
    
    
    /**
     * writes the data of a Todo instance into the database
     *
     * @param   Todo    $todo
     * @return  void
     * @author  Markus Buscher
     * @throws  \Exception
     */
    protected function saveTodo(Todo &$todo): void
    {
        // always set the update_ts timestamp
        $todo->setUpdateTs(new \DateTime());
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($todo);
        $entityManager->flush();
        $entityManager->refresh($todo);
    }
}
