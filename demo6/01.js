var system = require('system');
var page = require('webpage').create(); 
/*system.args.forEach(function(arg,i){
	console.log(arg);
	page.open(""+arg,function(){
		console.log('screen success!');
		page.render('screen.png');
		phantom.exit();
	});
});*/

page.open('https://www.baidu.com/',function(){
		console.log('welcome!');
		page.render('screen.png');
		phantom.exit();
});