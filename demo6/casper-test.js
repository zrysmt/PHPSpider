var casper = require('casper').create();
casper.start();
casper.thenOpen('http://www.baidu.com/', function () {
    casper.captureSelector('baidu.png', 'html');
});
casper.run();