<?php
namespace Codeception\Module;

use Codeception\Module;
use Symfony\Component\Yaml\Yaml;

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

    public $config = array('fixtures' => 'tests/_fixtures');

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
    public function symHaveEntryInDatabase($section,$data){
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

        symHaveEntriesInDatabase

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
    public function symHaveEntriesInDatabase($section,$data){
        if(!is_array($data)){
            throw new Exception("Non array passed");
        }
        $insertIds = array();

        foreach($data as $item){
            $id = $this->symHaveEntryInDatabase($section,$item);
            array_push($insertIds,$id);
        }
        return $insertIds;
    }


    /*

        Inserts multiple sections data into the database

        @returns associative array of entry IDs by section.

        Values can be in the format %section:position% to link entries togther.
        Positions start at 1, not 0.

        NB: The entries must be in the correct order for this, no logic will be applied to the run orde



        Ie: array(
            'people' => array(
                1,2,3
            ),
            'dogs' => array(
                5,6
            )
        );
    */

    public function symHaveInDatabase($data){
        $returnData = array();

        foreach($data as $section => $entries){
            //Process the data to remove references
            $entries = $this->processEntryData($entries,$returnData);
            $returnData[$section] = $this->symHaveEntriesInDatabase($section,$entries);
        }
        return $returnData;
    }



    /*

        Load a yml fixture into the Database

        Expects format

        people:
            -
                first-name: 'James'
                last-name: 'Bond'
            -
                first-name: 'Daniel'
                last-name: 'Craig'
        dogs:
            -
                name: 'Rover'
                owner: '%person:2%'

        @returns an array of the inserted entryIds and their sections

    */
    public function symHaveFixtureInDatabase($fixture){
        $data = Yaml::parse(file_get_contents($this->getFixturePath($fixture)));

        return $this->symHaveInDatabase($data);
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
            throw new \Exception("Unable to find entry");
        }
        if(__ENTRY_OK__ != $entry->setDataFromPost($data,$errors,false,true)){
            throw new \Exception("Error setting data");
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

        Process array to replace %section:position% with an ID

    */
    protected function processEntryData($data,$idLookup){
        if(is_array($data)){
            foreach($data as $key => $val){
                $data[$key] = $this->processEntryData($val,$idLookup);
            }
        }
        elseif(is_string($data)){
            if(substr_count($data,'%') == 2){
                $string = str_replace('%','',$data);
                $string = explode(':',$string);

                $section = $string[0];
                $pos = intval($string[1]) - 1;


                if(array_key_exists($section,$idLookup) && array_key_exists($pos,$idLookup[$section])){
                    $data = $idLookup[$section][$pos];
                }else{
                    throw new \Exception($section.":".$pos." Not found in:\n".print_r($idLookup,true));
                }
            }
        }
        return $data;
    }

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
        $data['id'] = $entry->get('id');
        return $data;
    }


    protected function getFixturePath($fixture){
        return codecept_root_dir() . $this->config['fixtures'] . "/" . $fixture .'.yml';
    }


}
?>
