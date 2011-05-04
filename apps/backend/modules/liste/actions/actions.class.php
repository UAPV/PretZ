<?php

/**
 * liste actions.
 *
 * @package    pretz
 * @subpackage liste
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class listeActions extends gzActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
   public function executeIndex(sfWebRequest $request)
   {
   }

   /*
    * Add a user in the list of a person
    */
   public function executeAjaxAdd(sfWebRequest $request)
   {
       $nomS = $request->getParameter('nomS');
       $nomR = $request->getParameter('nomR');

       $status = uapvProfileFactory::find ($nomR)->get('edupersonaffiliation');
       $uid = uapvProfileFactory::find ($nomR)->get('uid');

       $respons["error"] = false;

       $user = UserQuery::create()
          ->filterByUid($uid)
          ->findOne();

       // we check if the referant exists
       if($user == null)
       {
          $user = new User();
          $user->setName(uapvProfileFactory::find ($nomR)->get('cn'));
          $user->setUid($uid);
          $user->setStatus($status);
          $user->save();
       }

       $student = StudentQuery::create()
          ->filterByUid($nomS)
          ->findOne();
       
       // we check if the student exists
       if($student == null)
       {
          $student = new Student();
          $student->setName(uapvProfileFactory::find ($nomS)->get('cn'));
          $student->setUid(uapvProfileFactory::find ($nomS)->get('uid'));
          $student->save();
       }

       if(StudentUserQuery::alreadyExist($student->getId(),$user->getId()))
          $respons['already'] = true;
       else
       {
          // we save this student for this referant thanks the StudentUser table
          $save = new StudentUser();
          $save->setIduser($user->getId());
          $save->setIdstudent($student->getId());
          $save->save();

          $respons["nomS"] = $student->getName();
          $respons["nomR"] = $user->getName();
          $respons['already'] = false;
       }

       return $this->returnJSON($respons);
   }

   /*
    * Remove list of a person
    */
   public function executeAjaxDelete(sfWebRequest $request)
   {
       $nomR = $request->getParameter('nomR');
       $uid = uapvProfileFactory::find ($nomR)->get('uid');

       $user = UserQuery::create()
            ->filterByUid($uid)
            ->findOne();

       $listeStudent = StudentUserQuery::create()
                        ->filterByIdUser($user->getId())
                        ->find();

       // foreach students on this list, we remove his id in table StudentUser
       foreach($listeStudent as $student)
       {
           $s = StudentQuery::create()
                    ->filterById($student->getIdStudent())
                    ->findOne();
           $s->delete();
           $student->delete();
       }
       $respons['error'] = false;

       return $this->returnJSON($respons);
   }
}
