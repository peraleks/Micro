var numericCells = [1];
var	header = 2;

var wrap       = document.getElementById('wrap');
var table      = document.getElementById('table');
var sort       = document.getElementById('sort');
var inputParts = document.getElementById('input_parts');
var input 	   = document.getElementsByTagName('input');
var trTag 	   = document.getElementsByTagName('tr');
var checkbox   = document.querySelectorAll('.but');
var methodButton = document.querySelectorAll('.but_method');

var sortCellNumber;
var sortRowNumber;
var realRowIndex;
var lastRowIndex;
var scrolledValue;
var scrolled;
var rowsObject = {};
var cellsArray = [];
var	sortFlag   = [];
var lastIndex  = 1;
var rowsCount  = table.rows.length - header;
var cellsCount = table.rows[header + 1].cells.length
var offset 	   = inputParts.parentNode.getAttribute('colspan') -1;
var winWidth   = document.documentElement.clientWidth;

table.style.minWidth = winWidth + 'px';
window.scrollTo(1920, 0);

if (navigator.userAgent.indexOf('Firefox') > -1) {

	table.classList.add('firefox_table');
	var tdTag = document.getElementsByTagName('td');
	var thTag = document.getElementsByTagName('th');

	for (var i = 0; i < tdTag.length; i++) {
		tdTag[i].classList.add('firefox_tdh');
	}
	for (var i = 0; i < thTag.length; i++) {
		thTag[i].classList.add('firefox_tdh');
	}
}


for (var c = 0; c < cellsCount; c++) {
	cellsArray[c] = [];
	  sortFlag[c] = 1;
	
	for (var r = 0; r < rowsCount; r++) {
		cellsArray[c][r] = [];
		cellsArray[c][r]['value'] = table.rows[r + header].cells[c].innerHTML;
		cellsArray[c][r]['number'] = r;
		cellsArray[c][r]['hide'] = 0;
	}
}
function inArray(elem, array) {
	for (var i = 0; i < array.length; i++) {
		if (array[i] == elem) {
			return true;
		}
	}
	return false;
}

function compare(order, num) {
	if (order != 'desc') {
		if (!num) {
			return function compareAsc(a, b) {
				if (a.value == '') 	   return  1;
				if (b.value == '') 	   return -1;
				if (a.value > b.value) return  1;
				if (a.value < b.value) return -1;
			}
		} else {
			return function compareAscNumeric(a, b) {
				if (a.value == '') return  1;
				if (b.value == '') return -1;
				return  a.value - b.value;
			}
		}
	} else {
		if (!num) {
			return	function compareDesc(a, b) {
				if (a.value > b.value) return -1;
				if (a.value < b.value) return  1;
			}
		} else {
			return	function compareDescNumeric(a, b) {
				return  b.value - a.value;
			}
		}
	}
}

sort.onclick = function(event) {
	scrolle(this);
	var target = event.target;
	if (target.tagName != 'TH') {
		target = target.parentNode;
	}

	if (sortFlag[target.cellIndex] == 1) {
		cellsArray[target.cellIndex].sort(compare('asc', inArray(target.cellIndex, numericCells)));
		target.innerHTML = '&#8593';
		target.classList.add('sort_pointer');
	}
	else {
		cellsArray[target.cellIndex].sort(compare('desc', inArray(target.cellIndex, numericCells)));
		target.innerHTML = '&#8595';
	}

	if (target.cellIndex != lastIndex) {
		target.parentNode.children[lastIndex].innerHTML = '';
		target.parentNode.children[lastIndex].classList.remove('sort_pointer');
		sortFlag[lastIndex] = 1;
	}
	lastIndex = target.cellIndex;
	sortFlag[target.cellIndex] = sortFlag[target.cellIndex] * -1;
	tableRewrite();
}

function  tableRewrite() {
	if(lastRowIndex) {
		table.rows[lastRowIndex].classList.remove('highlight');
	}
	rowsObject = {};
	for (var r = 0; r < rowsCount; r++) {
		var hide = 0;

		for (var c = 0; c < cellsCount; c++) {

			for (var h = 0; h < rowsCount; h++) {

				if (cellsArray[lastIndex][r]['number'] == cellsArray[c][h]['number']) {
					hide += cellsArray[c][h]['hide'];
				}
			}
		}
		if (hide > 0) {	table.rows[r + header].classList.add('hidden');	}
			     else { table.rows[r + header].classList.remove('hidden'); }

		sortCellNumber = table.rows[r + header].cells[1].innerHTML;
		sortRowNumber  = cellsArray[lastIndex][r]['number'] + 1;

		if (sortCellNumber != sortRowNumber) {
			rowsObject[sortCellNumber] = table.rows[r + header].innerHTML;

			if (sortRowNumber in rowsObject) {
				table.rows[r + header].innerHTML = rowsObject[sortRowNumber];
			}
			else {
				for (var s = r + header; s < table.rows.length; s++) {

					if (table.rows[s].cells[1].innerHTML == sortRowNumber) {
						table.rows[r + header].innerHTML = table.rows[s].innerHTML;
						break;
					}
				}
			}
		}
		if (cellsArray[lastIndex][r]['number'] == realRowIndex) {
			table.rows[r + header].classList.add('highlight');
			lastRowIndex = r + header;
		}
	}
	if (scrolled) {
		var rect = scrolled.getBoundingClientRect();
		window.scrollBy(rect.left - scrolledValue, 0);
	}
}

function inputHandler(value, cell) {
	for (var i = 0; i < rowsCount; i++) {
		if (value == '') {
			cellsArray[cell][i]['hide'] = 0;
		}

		else if (cellsArray[cell][i]['value'].search(value) < 0) {
				 cellsArray[cell][i]['hide'] = 1;
		} else { cellsArray[cell][i]['hide'] = 0; }
	}
	tableRewrite();
}


for (var i = 0; i < input.length; i++) {

	input[i].onkeyup = function() {
		scrolle(this);
		if (this.value == ' ') {
			this.value = '';
		}
		this.parentNode.cellIndex < 4
		?
		inputHandler(this.value,  this.parentNode.cellIndex)
		:
		inputHandler(this.value, +this.parentNode.cellIndex + +offset);
	}
}

for (var i = header; i < trTag.length; i++) {
	
	trTag[i].onclick = function(event) {
		realRowIndex = cellsArray[lastIndex][this.rowIndex - header]['number'];

		if (lastRowIndex) {
			table.rows[lastRowIndex].classList.remove('highlight');
		}
		lastRowIndex = this.rowIndex;
		this.classList.add('highlight');
	}
}

for (var k = 0; k < checkbox.length; k++) {

	checkbox[k].onclick = function() {

		for (var i = header; i < table.rows.length; i++) {
			table.rows[i].cells[this.getAttribute('data-cell')].classList.toggle('minimize');
		}
	}
}

for (var m = 0; m < methodButton.length; m++) {

	methodButton[m].onclick = function() {
		scrolle(this);
		
		this.classList.toggle('but_method_highlight');

		this.classList.contains('but_method_highlight')
		?
		inputHandler('.', +this.parentNode.cellIndex + +offset)
		:
		inputHandler( '', +this.parentNode.cellIndex + +offset);
	}
}

wrap.onclick = function(event) {
	if (event.target == this) {
		var rect = table.getBoundingClientRect();
		if (rect.left > 0) {
			window.scrollBy(rect.left, 0);
		}
		if (rect.right < winWidth) {
			window.scrollBy(rect.right - winWidth, 0);
		}
	}
}

function scrolle(self) {
				var scr = self.getBoundingClientRect();
	scrolledValue = scr.left;
	scrolled = self;
}

console.dir(methodButton);