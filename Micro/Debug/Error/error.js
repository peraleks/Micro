window.onload = function(){
var trace = document.querySelectorAll('.but_trace');
for (var i = 0; i < trace.length; i++) {
	trace[i].onclick = function() {
		console.dir(this.parentNode.parentNode.children[2].classList.toggle('hidden'));
	};
}
};