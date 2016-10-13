var system = require('system');
var page = require('webpage').create();
var fs = require('fs');

phantom.outputEncoding = "gbk"; //解决中文乱码

/**
 * 异步编程实例Demo  --CasperJS
 * 见casper-test.js
 */

//读取文件
var cwd = fs.absolute(".");
console.log(cwd);

var filePath = "url-02.txt";
// var file = fs.open(filePath, 'r');
// file.close();
var content = fs.read(filePath);
var urlArr = content.split('\n');
var len = urlArr.length,
    i = 0;

page.open(urlArr[i], function(status) {
    console.log(status);
    var url = page.url;
    console.log('URL: ' + url);
    page.render(i + '.png');
    i++;
    setTimeout(function() {
        page.open(urlArr[i], function(status) {
            console.log(status);
            var url = page.url;
            console.log('URL: ' + url);
            page.render(i + '.png');
            i++;
        });
    }, 5000);
});


if (i === len - 1) {
    phantom.exit();
}

page.onConsoleMessage = function(msg) {
    console.log("CONSOLE MESSAGE:" + msg);
};

page.onResourceError = function(resourceError) {
    console.log('Unable to load resource (#' + resourceError.id + 'URL:' + resourceError.url + ')');
    console.log('Error code: ' + resourceError.errorCode + '. Description: ' + resourceError.errorString);
};

/*system.args.forEach(function(arg,i){
	console.log(arg);
	page.open(""+arg,function(){
		page.render('screen.png');
		var title = page.evalute(function(){
			return document.title;
		});
		console.log('Page title: '+title);
		phantom.exit();
	});
});*/

/**
 * 输入参数
 */
/*system.args.forEach(function(arg,i){
	console.log(arg);
});*/

/**
 * 打开浏览器显示标题
 */
/*page.open("https://www.baidu.com/", function(status) {
    console.log(status);
    page.render('screen.png');
    var title = page.evaluate(function() {
        return document.title;
    });
    console.log('Page title: ' + title);
    phantom.exit();
});*/

/**
 * 只是截图
 */
/*
page.viewportSize = {
  width: 1366,
  height: 800
};
var url = "https://www.baidu.com/";
var urls = ["https://www.baidu.com/", "https://zrysmt.github.io/"];
page.open(urls[0], function() {
    console.log('welcome!');
    page.render('screen.png');
    setTimeout(function() {
        phantom.exit();
    }, 500);

});*/
/**
 * 加入jquery
 */
/*page.open("https://www.baidu.com/", function(status) {
    console.log(status);
    page.render('screen.png');
    page.includeJs("http://cdn.bootcss.com/jquery/1.12.4/jquery.js", function() {
        // page.evaluate(function() {
        //     $(".bg s_btn").click();
        // });
        var title = page.evaluate(function() {
			$('.s_ipt').val('123');
            $(".bg s_btn").click();
            return document.title;
        });
        console.log('Page title: ' + title);
        phantom.exit();
    });

});*/
