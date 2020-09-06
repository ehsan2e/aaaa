//mini navigation
function miniNav() {
    "use strict";
    var y = document.getElementById("hamBar");
        if (y.className === "miniNav") {
        y.className += " active";
        } else {
        y.className = "miniNav";
    }
    var l = document.getElementById("learning");
    if (l.className === "mainMenu") {
        l.className += " responsive";
        } else {
        l.className = "mainMenu";
        }
     var s = document.getElementById("services");
     if (s.className === "mainMenu") {
        s.className += " responsive";
        } else {
        s.className = "mainMenu";
        }
     var p = document.getElementById("packages");
     if (p.className === "mainMenu") {
        p.className += " responsive";
        } else {
        p.className = "mainMenu";
        }
     var pr = document.getElementById("products");
     if (pr.className === "mainMenu") {
        pr.className += " responsive";
        } else {
        pr.className = "mainMenu";
        }
     var su = document.getElementById("support");
     if (su.className === "mainMenu") {
        su.className += " responsive";
        } else {
        su.className = "mainMenu";
        }
    
}
//slide Show Script

var slideIndex = [1,1];
var slideId = ["mySlides1", "mySlides2"]
showDivs(1, 0);
showDivs(1, 1);

function plusDivs(n, no) {
  showDivs(slideIndex[no] += n, no);
}

function showDivs(n, no) {
  var i;
  var x = document.getElementsByClassName(slideId[no]);
  if (n > x.length) {slideIndex[no] = 1}
  if (n < 1) {slideIndex[no] = x.length}
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  x[slideIndex[no]-1].style.display = "block";  
}

//tabs Script
function openBenefit(evt, BenefitName) {
  // Declare all variables
    "use strict";
    var i, tabcontent, tablinks, defaultTabContent;

  // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i += 1) {
        tabcontent[i].style.display = "none";
    }
    defaultTabContent = document.getElementsByClassName("defaultTabContent");
    for (i = 0; i < defaultTabContent.length; i += 1) {
        defaultTabContent[i].style.display = "none";
    }

  // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i += 1) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

  // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(BenefitName).style.display = "block";
    evt.currentTarget.className += " active";
}
//language button
var x, i, j, l, ll, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("customSelect");
l = x.length;
for (i = 0; i < l; i += 1) {
    selElmnt = x[i].getElementsByTagName("select")[0];
    ll = selElmnt.length;
  /*for each element, create a new DIV that will act as the selected item:*/
    a = document.createElement("DIV");
    a.setAttribute("class", "select-selected");
    a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
    x[i].appendChild(a);
  /*for each element, create a new DIV that will contain the option list:*/
    b = document.createElement("DIV");
    b.setAttribute("class", "select-items select-hide");
    for (j = 1; j < ll; j += 1) {
    /*for each option in the original select element,
    create a new DIV that will act as an option item:*/
        c = document.createElement("DIV");
        c.innerHTML = selElmnt.options[j].innerHTML;
        c.addEventListener("click", function (e) {
        /*when an item is clicked, update the original select box,
        and the selected item:*/
            "use strict";
            var y, i, k, s, h, sl, yl;
            s = this.parentNode.parentNode.getElementsByTagName("select")[0];
            sl = s.length;
            h = this.parentNode.previousSibling;
            for (i = 0; i < sl; i += 1) {
                if (s.options[i].innerHTML === this.innerHTML) {
                    s.selectedIndex = i;
                    h.innerHTML = this.innerHTML;
                    y = this.parentNode.getElementsByClassName("same-as-selected");
                    yl = y.length;
                    for (k = 0; k < yl; k += 1) {
                        y[k].removeAttribute("class");
                    }
                    this.setAttribute("class", "same-as-selected");
                    break;
                }
            }
            h.click();
        });
        b.appendChild(c);
    }
    x[i].appendChild(b);
    a.addEventListener("click", function (e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
        "use strict";
        e.stopPropagation();
        closeAllSelect(this);
        this.nextSibling.classList.toggle("select-hide");
        this.classList.toggle("select-arrow-active");
    });
}
function closeAllSelect(elmnt) {
  /*A function that will close all select boxes in the document,
  except the current select box:*/
    "use strict";
    var x, y, i, xl, yl, arrNo = [];
    x = document.getElementsByClassName("select-items");
    y = document.getElementsByClassName("select-selected");
    xl = x.length;
    yl = y.length;
    for (i = 0; i < yl; i += 1) {
        if (elmnt === y[i]) {
            arrNo.push(i);
        } else {
            y[i].classList.remove("select-arrow-active");
        }
    }
    for (i = 0; i < xl; i += 1) {
        if (arrNo.indexOf(i)) {
            x[i].classList.add("select-hide");
        }
    }
}
/*if the user clicks anywhere outside the select box,
then close all select boxes:*/
document.addEventListener("click", closeAllSelect);

