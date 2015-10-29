<?php
$I = new FunctionalTester($scenario);
$I->wantTo('I want to load SymphonyCMSDb Module');

$I->assertEquals($I->symphonyCMSDBTest(),'Hello World');

$I->assertNotNull( \EntryManager::create());
