<?php
//use \FunctionalTester;

class SymphonyCMSDbCest
{
    public function _before(\FunctionalTester $I)
    {
    }

    public function _after(\FunctionalTester $I)
    {

    }

    // Can retrieve an item
    public function can_retrieve_a_section_entry(\FunctionalTester $I)
    {
        $entry = $I->symGetSectionEntryByID('people',1);
        $I->assertEquals($entry['first-name']['handle'],'james');
    }

    //Can Insert data
    public function can_insert_entry_into_symphony(\FunctionalTester $I){

        $id = $I->symHaveInDatabaseSingle('dogs',array(
            'name'=>'mark',
            'owner'=>'1',
            'born'=>'22/10/2010'
        ));

        $I->assertNotNull($id);

        $entry = $I->symGetSectionEntryByID('dogs',$id);

        $I->assertEquals($entry['name']['handle'],'mark');
        $I->assertEquals($entry['owner']['relation_id'],'1');
        $I->assertContains('2010-10-22',$entry['born']['date']);

    }

    public function can_insert_multiple_into_symphony(\FunctionalTester $I){

        $idArr = $I->symHaveInDatabase('dogs',array(
            array(
                'name'=>'mark',
                'owner'=>'1',
                'born'=>'22/10/2010'
            ),
            array(
                'name'=>'james',
                'owner'=>'1',
                'born'=>'22/1/2012'
            )
        ));
        $I->assertEquals(count($idArr),2);

        $I->seeInDatabase('sym_entries_data_6',array('handle'=>'mark'));
        $I->seeInDatabase('sym_entries_data_6',array('handle'=>'james'));

        $entry = $I->symGetSectionEntryByID('dogs',$idArr[0]);
        $I->assertEquals($entry['name']['handle'],'mark');

        $entry = $I->symGetSectionEntryByID('dogs',$idArr[1]);
        $I->assertEquals($entry['name']['handle'],'james');

    }


    public function can_update_database_record(\FunctionalTester $I){
        $I->symUpdateDatabaseRecord('people',1,array('first-name'=>'jonathan','cool'=>'no','favourite-colour'=>array('Green')));

        $entry = $I->symGetSectionEntryByID('people',1);

        $I->assertEquals($entry['first-name']['handle'],'jonathan');
        $I->assertEquals($entry['favourite-colour']['handle'],'green');
        $I->assertEquals($entry['cool']['value'],'no');

    }

}
