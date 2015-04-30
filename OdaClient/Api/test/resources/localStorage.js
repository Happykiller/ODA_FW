test( "[WEBAPP]$.functionsStorage.isStorageAvaible", function() {
    var retourStorage = $.functionsStorage.isStorageAvaible();
    equal(retourStorage, true, "Test OK : Passed! (check)" );
});

test( "[WEBAPP]$.functionsStorage.set", function() {
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST1','hello');
    equal(retourStorage, true, "Test OK : Passed! (create, without ttl)" );
    var retourStorage = $.functionsStorage.remove('ODA_QUNIT_TEST1');
    
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST2','hello',1);
    equal(retourStorage, true, "Test OK : Passed! (create, with ttl 1)" );
    var retourStorage = $.functionsStorage.remove('ODA_QUNIT_TEST2');
});

test( "[WEBAPP]$.functionsStorage.get", function() {
    var retourStorage = $.functionsStorage.get('ODA_QUNIT_TEST1','hello');
    equal(retourStorage, 'hello', "Test OK : Passed! (get avec default)" );
    var retourStorage = $.functionsStorage.remove('ODA_QUNIT_TEST1');
    
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST2','hello');
    var retourStorage = $.functionsStorage.get('ODA_QUNIT_TEST2');
    equal(retourStorage, 'hello', "Test OK : Passed! (get)" );
    var retourStorage = $.functionsStorage.remove('ODA_QUNIT_TEST2');
    
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST3','hello',1);
    $.functionsLib.sleep(1000);
    var retourStorage = $.functionsStorage.get('ODA_QUNIT_TEST3');
    equal(retourStorage, null, "Test OK : Passed! (get expired var)" );
});

test( "[WEBAPP]$.functionsStorage.setTtl", function() {
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST1','hello');
    
    var retourStorage = $.functionsStorage.setTtl('ODA_QUNIT_TEST1',1);
    
    $.functionsLib.sleep(42);
    
    var retourStorage = $.functionsStorage.getTtl('ODA_QUNIT_TEST1');
    ok(((retourStorage != 0)||(retourStorage != null)), "Test OK : Passed! (get ttl "+retourStorage+" )" );
    
    $.functionsLib.sleep(1000);
    
    var retourStorage = $.functionsStorage.get('ODA_QUNIT_TEST1');
    equal(retourStorage, null, "Test OK : Passed! (get expired var, after set ttl)" );
});

test( "[WEBAPP]$.functionsStorage.remove", function() {
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST1','hello');
    
    var retourStorage = $.functionsStorage.remove('ODA_QUNIT_TEST1');
    
    var retourStorage = $.functionsStorage.get('ODA_QUNIT_TEST11');
    equal(retourStorage, null, "Test OK : Passed! (remove)" );
});

test( "[WEBAPP]$.functionsStorage.getIndex", function() {
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST1','hello');
    var retourStorage = $.functionsStorage.set('ODA_QUNIT_TEST2','hello');
    
    var retourStorage = $.functionsStorage.getIndex('ODA_QUNIT_TEST');

    equal(retourStorage.length, 2, "Test OK : Passed! (get index)" );
    
    var retourStorage = $.functionsStorage.remove('ODA_QUNIT_TEST1');
    var retourStorage = $.functionsStorage.remove('ODA_QUNIT_TEST2');
});