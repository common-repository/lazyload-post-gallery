"use strict";
var $_ = new Object, Node, NodeList, HTMLCollection;
$_ = {
    guid: 0,
    eventQ: [],
    NP: Node.prototype,
    NLP: NodeList.prototype,
    // SELECTORS WRAPPERS
    tn: function (tn) {
        return document.getElementsByTagName(tn);
    },
    id: function (id) {
        return document.getElementById(id);
    },
    cn: function (cn) {
        return document.getElementsByClassName(cn);
    },
    na: function (n) {
        return document.getElementsByName(n);
    },
    qa: function (q) {
        return document.querySelectorAll(q);
    },
    qs: function (q) {
        return document.querySelector(q);
    },
    ce: function (el) {
        return document.createElement(el);
    },
    ctn: function (tn) {
        return document.createTextNode(tn);
    },
    event: {
        listen: function (node, eventname, fn, F) {
            
            var fname = $_.fname(fn);
            if (fname !== "") {
                node.attr("data-" + eventname + "-" + fname, $_.guid);
                $_.eventQ[$_.guid] = $_.renameFunction(fname, F);
                node.addEventListener(eventname, $_.eventQ[$_.guid], false);
                ++$_.guid;
            } else {
                node.addEventListener(eventname, F, false);
            }
        },
        add: function (node, eventname, fn) {
            
            function F(e) {
                fn.apply(e.target, arguments);
            }

            F.bind(node);
            $_.event.listen(node, eventname, fn, F);
        },
        addWithData: function (node, eventname, data, fn) {
            function F(e) {
                e.data = data;
                fn.apply(e.target, arguments);
                e.stopPropagation();
            }

            F.bind(node);
            $_.event.listen(node, eventname, fn, F);
        },
        delegate: function (parent, eventname, sel, data, fn) {
            function F(e) {
                e.data = data;
                if (sel !== null) {
                    var ValidTargets = parent.qa(sel);
                    if (ValidTargets.contains(e.target) === false) {
                        return;
                    }
                    fn.apply(e.target, arguments);
                    e.stopPropagation();
                }
            }

            F.bind(parent);
            $_.event.listen(parent, eventname, fn, F);
        }
    },
    fname: function (fun) {
        var ret = fun.toString();
        ret = ret.substr('function '.length);
        ret = ret.substr(0, ret.indexOf('('));
        return ret;
    },
    renameFunction: function (name, fn) {
        var Function;
        return (new Function("return function (call) { return function " + name +
                " () { return call(this, arguments) }; };")())(Function.apply.bind(fn));
    }
};

$_.NP.qs = function (q) {
    return this.querySelector(q);
};

$_.NP.qa = function (q) {
    return document.querySelectorAll(q, this);
};

// Crée un fragment DOM pour plusieurs insertions évitant refusion
// ==============================================================
$_.fragment = document.createDocumentFragment();

// =============================================================================
// .addClass()
// -----------------------------------------------------------------------------
// Obtenir la valeur d'une propriété de style pour le premier élément de l'ensemble 
// des éléments appariés ou définir une ou plusieurs propriétés de CSS pour chaque 
// élément identifié.
// -----------------------------------------------------------------------------

$_.NP.addClass = function () {
    var args = [].slice.apply(arguments);
    var cl = this.classList;
    cl.add.apply(cl, args);
    return this;
};

$_.NLP.addClass = function () {
    var args = [].slice.apply(arguments);
    for (var i = 0, L = this.length; i < L; i++) {
        var cl = this[i].classList;
        cl.add.apply(cl, args);
    }
    return this;
};

HTMLCollection.prototype.addClass = $_.NLP.addClass;

// =============================================================================
// .attr()
// -----------------------------------------------------------------------------
// Obtenir la valeur d'un attribut pour le premier élément dans l'ensemble des 
// éléments appariés ou définir un ou plusieurs attributs pour chaque élément identifié.
// -----------------------------------------------------------------------------

$_.NP.attr = function (a, v) {
    if (arguments.length === 2) {
        this.setAttribute(a, v);
        return this;
    }

    if (typeof a === 'string') {
        return this.getAttribute(a);
    }

    if (typeof a === 'object') {
        for (var k in a) {
            this.setAttribute(k, a[k]);
        }
        return this;
    }
};

$_.NLP.attr = function () {
    var args = [].slice.apply(arguments);

    for (var i = 0, L = this.length; i < L; i++) {
        this[i].attr.apply(this[i], args);
    }

    return this;
};

HTMLCollection.prototype.attr = $_.NLP.attr;

$_.cC = function (property) {
    var name = property.replace(/-(\w)/g, function _replace($1, $2) {
        return $2.toUpperCase();
    });
    return name;
};

$_.NLP.contains = function (node) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] === node) {
            return true;
        }
    }
    return false;
};

// =============================================================================
// .css()
// -----------------------------------------------------------------------------
// Retourne la valeur d'une propriété de style pour la première élément de l'ensemble 
// des éléments appariés ou définir un ou plusieurs propriétés de CSS pour chaque 
// élément identifié.
// -----------------------------------------------------------------------------
$_.NP.css = function (c, v) {
    if (arguments.length === 2) {
        this.style[$_.cC(c)] = v;
        return this;
    }

    if (typeof c === 'string') {
        return window.getComputedStyle(this).getPropertyValue(c);
    }

    if (typeof c === 'object') {
        for (var k in c) {
            this.style[$_.cC(k)] = c[k];
        }
        return this;
    }
};

$_.NLP.css = function () {
    var args = [].slice.apply(arguments);

    for (var i = 0, L = this.length; i < L; i++) {
        this[i].css.apply(this[i], args);
    }
    return this;
};

HTMLCollection.prototype.css = $_.NLP.css;

// =============================================================================
// .on()
// -----------------------------------------------------------------------------
$_.NP.on = function (arg1, arg2, arg3, arg4) {
    if (arguments.length === 2) {
        $_.event.add(this, arg1, arg2);
        return this;
    }

    if (arguments.length === 3) {
        if ($_.isString(arg2)) { // on(event, selector, function)
            $_.event.delegate(this, arg1, arg2, null, arg3);
            return this;
        } else { // on(event, data, function)
            $_.event.addWithData(this, arg1, arg2, arg3);
            return this;
        }
    }
    $_.event.delegate(this, arg1, arg2, arg3, arg4);
    return this;
};

$_.NLP._click = function (f) {
    for (var i = 0, L = this.length; i < L; i++) {
        this[i]._click(f);
    }
    return this;
};

$_.NP.off = function (eventname, fn) {
    var fname = $_.fname(fn);
    if (fname !== "") {
        var id = this.attr("data-" + eventname + "-" + fname);
        this.removeEventListener(eventname, $_.eventQ[id], false);
        $_.eventQ[id] = null;
        this.removeAttribute("data-" + eventname + "-" + fname);
    }
    return this;
};

HTMLCollection.prototype._click = $_.NLP._click;

// =============================================================================
// .rmListeners()
// -----------------------------------------------------------------------------
// Supprime tous les listeners d'un node et des enfants du node 
//
// Exemple: $_.id("myID").rmListeners()
// -----------------------------------------------------------------------------

$_.NP.rmListeners = function () {
    var Clone = this.cloneNode(true);
    this.parentNode.replaceChild(Clone, this);
};

// =============================================================================
// .hasClass()
// -----------------------------------------------------------------------------
// Retourne la valeur d'une propriété de style pour la première élément de l'ensemble 
// des éléments appariés ou définir un ou plusieurs propriétés de CSS pour chaque 
// élément identifié.
// -----------------------------------------------------------------------------

$_.NP.hasClass = function (c) {
    return this.classList.contains(c);
};

$_.NLP.hasClass = function () {
    var args = [].slice.apply(arguments);
    for (var i = 0, L = this.length; i < L; i++) {
        var cl = this[i].classList;
        if (cl.contains.apply(cl, args) === true) {
            return true;
        }
    }
    return false;
};

HTMLCollection.prototype.hasClass = $_.NLP.hasClass;

// =============================================================================
// .html()
// var test = $_.id("body").html();
// console.log(test);
// -----------------------------------------------------------------------------
// Obtenir le contenu HTML du premier élément de l'ensemble d'éléments appariés 
// ou définir le contenu HTML de chaque élément identifié.
// -----------------------------------------------------------------------------

$_.NP.html = function (h) {
    if (arguments.length === 0) {
        return this.innerHTML;
    }
    this.innerHTML = h;
};

$_.NLP.html = function (h) {
    if (arguments.length === 0) {
        return this[0].innerHTML;
    }

    for (var i = 0, L = this.length; i < L; i++) {
        var N = this[i];
        N.innerHTML = h;
    }
    return this;
};

HTMLCollection.prototype.html = $_.NLP.html;

// =============================================================================
// .prop()
// -----------------------------------------------------------------------------
// Obtenir la valeur d'un bien pour le premier élément de l'ensemble des éléments 
// appariés ou définir un ou plusieurs propriétés pour chaque élément identifié.
// -----------------------------------------------------------------------------

$_.NP.prop = function (p, v) {
    if (arguments.length === 2) {
        this[p] = v;
        return this;
    }

    if (typeof p === 'string') {
        return this[p];
    }

    if (typeof p === 'object') {
        for (var k in p) {
            this[k] = p[k];
        }
        return this;
    }
};

$_.NLP.prop = function () {
    var args = [].slice.apply(arguments);

    for (var i = 0, L = this.length; i < L; i++) {
        this[i].prop.apply(this[i], args);
    }
    return this;
};

HTMLCollection.prototype.prop = $_.NLP.prop;

// =============================================================================
// .removeClass()
// -----------------------------------------------------------------------------
// Suppression d'une seule classe, plusieurs classes, ou toute classes de chaque 
// élément de l'ensemble des éléments identifiés.
// -----------------------------------------------------------------------------

$_.NP.removeClass = function () {
    if (arguments.length === 0) {
        this[i].className = "";
        return this;
    }
    var args = [].slice.apply(arguments);
    var cl = this.classList;
    cl.remove.apply(cl, args);
    return this;
};

$_.NLP.removeClass = function () {
    if (arguments.length === 0) {
        for (var i = 0, L = this.length; i < L; i++) {
            this[i].className = "";
        }
        return this;
    }
    var args = [].slice.apply(arguments);
    for (var i = 0, L = this.length; i < L; i++) {
        this[i].classList.remove.apply(this[i].classList, args);
    }
    return this;
};

HTMLCollection.prototype.removeClass = $_.NLP.removeClass;

// =============================================================================
// .toggleClass()
// -----------------------------------------------------------------------------
// Ajouter ou supprimer une classe de l'élément identifié, soit en fonction de 
// la présence de la classe ou de la valeur de l'argument de la force.
// -----------------------------------------------------------------------------

$_.NP.toggleClass = function (c, force) {
    var cl = c.split(' '),
            ii = cl.length;

    if (arguments.length === 1) {
        for (var i = 0; i < ii; i++) {
            this.classList.toggle(cl[i]);
        }
        return this;
    }

    for (var i = 0; i < ii; i++) {
        this.classList.toggle(cl[i], force);
    }
    return this;
};

$_.NLP.toggleClass = function (c, force) {
    var L = this.length;

    if (arguments.length === 1) {
        for (var i = 0; i < L; i++) {
            this[i].toggleClass(c);
        }
        return this;
    }

    for (var i = 0; i < L; i++) {
        this[i].toggleClass(c, force);
    }
    return this;
};

HTMLCollection.prototype.toggleClass = $_.NLP.toggleClass;

// =============================================================================
// .replaceClass()
// -----------------------------------------------------------------------------
// Remplacer une classe de l'élément identifié.
// -----------------------------------------------------------------------------

$_.NP.replaceClass = function (o, n) {
    this.classList.remove(o);
    this.classList.add(n);
    return this;
};

$_.NLP.replaceClass = function (o, n) {
    for (var i = 0, L = this.length; i < L; i++) {
        this[i].removeClass(o);
        this[i].addClass(n);
    }
    return this;
};

HTMLCollection.prototype.replaceClass = $_.NLP.replaceClass;

// =============================================================================
// .getJSON()
// -----------------------------------------------------------------------------
// Description : Ajax request GET
// Param : URL - String with or without params
// Param : success - function callback

$_.getJSON = function (URL, success) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', URL, true);
    var data = {};
    xhr.onreadystatechange = function (e) {
        if (xhr.readyState === 4 && xhr.status === 200) {
            data = JSON.parse(xhr.responseText);
            success.call(this, data);
        }
    };
    xhr.send(null);
};



// =============================================================================
$_.NLP.forEach = Array.prototype.forEach;
HTMLCollection.prototype.forEach = Array.prototype.forEach;




