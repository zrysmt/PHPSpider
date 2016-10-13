var fs = require('fs');

var casper = require('casper').create();

phantom.outputEncoding = "gbk"; //解决中文乱码

var filePath = "url-02.txt";
var content = fs.read(filePath);
var urlArr = content.split('\n');
casper.start();

for (var i = 0; i < urlArr.length; i++) {
    casper.thenOpen(urlArr[i], function() {
        this.echo('Page title: ' + this.getTitle());
    });
}

casper.run();
// phantom.exit();
