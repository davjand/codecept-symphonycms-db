<?php
namespace Codeception\Module;

use Codeception\Module;

//Core Symphony
define('DOMAIN',NULL);
require_once(DOCROOT."symphony/lib/boot/bundle.php");
require_once(CORE . '/class.frontend.php');

//Classes
include_once(TOOLKIT.'/class.extensionmanager.php'); //fixes a bug in symphony by including this
include_once(TOOLKIT.'/class.sectionmanager.php');
include_once(TOOLKIT.'/class.entrymanager.php');
include_once(TOOLKIT.'/class.fieldmanager.php');


class SymphonyCMSDb extends Module{

    // HOOK: used after configuration is loaded
    public function _initialize() {
        //Init Symphony
        \Frontend::instance();
    }

    // HOOK: on every Guy class initialization
    public function _cleanup() {
    }

    // HOOK: before each suite
    public function _beforeSuite($settings = array()) {
    }

    // HOOK: after suite
    public function _afterSuite() {
    }

    // HOOK: before each step
    public function _beforeStep(\Codeception\Step $step) {
    }

    // HOOK: after each  step
    public function _afterStep(\Codeception\Step $step) {
    }

    // HOOK: before test
    public function _before(\Codeception\TestCase $test) {
    }

    // HOOK: after test
    public function _after(\Codeception\TestCase $test) {
    }

    // HOOK: on fail
    public function _failed(\Codeception\TestCase $test, $fail) {
    }



    /*


        FUNCTIONS


    */

    /*
        symHaveInDatabase
        @param $section - The section handle
        @param $data - The data to insert in the format array(field-name=>value)

        Inserts Data into the symphony database
        Expects key value format

    */
    public function symHaveInDatabaseSingle($section,$data){
        $sectionId = \SectionManager::fetchIDFromHandle($section);

        $entry = \EntryManager::create();
        $entry->set('section_id',$sectionId);
        if($entry->setDataFromPost($data) == __ENTRY_FIELD_ERROR__){
            throw('Error setting data');
        }
        $entry->commit();
        return $entry->get('id');

    }

    /*

        symHaveInDatabase

        @param $section - Section Handle
        @param $data - Data array

        Expects a key value array in the following format:
        array(
            array(
                field-name: value
            )
        )


        @returns an array of inserted entryIds


    */
    public function symHaveInDatabase($section,$data){
        if(!is_array($data)){
            throw "Non array passed";
        }
        $insertIds = array();

        foreach($data as $item){
            $id = $this->symHaveInDatabaseSingle($section,$item);
            array_push($insertIds,$id);
        }
        return $insertIds;
    }



    /*

        symUpdateDatabaseRecord

        Update a record in the database
        @param $section - The Section Handle
        @param $entryId - The Entry ID to update
        @param $data - array(field-name => value)


    */
    public function symUpdateDatabaseRecord($section,$entryId,$data){
        $sectionId = \SectionManager::fetchIDFromHandle($section);
        $error;

        $entry = \EntryManager::fetch( $entryId );
		$entry = $entry[0];
        if( !$entry instanceof \Entry ){
            throw "Unable to find entry";
        }
        if(__ENTRY_OK__ != $entry->setDataFromPost($data,$errors,false,true)){
            throw "Error setting data";
        }
        $entry->commit();
    }




    /*

        symGetSectionEntryByID

        Returns a key value array of the data from a symphony entry

        @param $section - Section Handle
        @

    */
    public function symGetSectionEntryByID($section,$entryId){

        $sectionId = \SectionManager::fetchIDFromHandle($section);
        $entries = \EntryManager::fetch($entryId,$sectionId);
        $entry = count($entries) > 0 ? $entries[0] : null;

        return $this->buildEntryArray($entry,$sectionId);

    }



    /*

        Simple Test function

    */
    public function symphonyCMSDbTest(){
        return 'Hello World';
    }


    /*


        HELPER FUNCTIONS


    */

    /*

        Convert an entry into a key value array

    */
    protected function buildEntryArray($entry,$sectionId){
        $data = array();
        if($entry != null){
            $schema = \FieldManager::fetchFieldsSchema($sectionId);
            foreach($schema as $field){
                $data[$field['element_name']] = $entry->getData($field['id']);
            }
        }
        return $data;
    }


}
?>
