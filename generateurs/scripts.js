let bases = {
  ":'(": "ohill.gif",
  ":(": "frown.gif",
  ":)": "smile.gif",
  ":/": "ohwell.gif",
  ":??:": "confused.gif",
  ":D": "biggrin.gif",
  ":o": "redface.gif",
  ":p": "tongue.gif",
  ";)": "wink.gif",
  ":24:": "smilies/24.gif",
  ":ange:": "smilies/ange.gif",
  ":benetton:": "smilies/benetton.gif",
  ":bic:": "smilies/bic.gif",
  ":bounce:": "smilies/bounce.gif",
  ":bug:": "smilies/bug.gif",
  ":calimero:": "smilies/calimero.gif",
  ":crazy:": "smilies/crazy.gif",
  ":cry:": "smilies/cry.gif",
  ":dtc:": "smilies/dtc.gif",
  ":eek:": "smilies/eek.gif",
  ":eek2:": "smilies/eek2.gif",
  ":evil:": "smilies/evil.gif",
  ":fou:": "smilies/fou.gif",
  ":foudtag:": "smilies/foudtag.gif",
  ":fouyaya:": "smilies/fouyaya.gif",
  ":fuck:": "smilies/fuck.gif",
  ":gratgrat:": "smilies/gratgrat.gif",
  ":hap:": "smilies/hap.gif",
  ":gun:": "smilies/gun.gif",
  ":hebe:": "smilies/hebe.gif",
  ":heink:": "smilies/heink.gif",
  ":hello:": "smilies/hello.gif",
  ":hot:": "smilies/hot.gif",
  ":int:": "smilies/int.gif",
  ":jap:": "smilies/jap.gif",
  ":kaola:": "smilies/kaola.gif",
  ":lol:": "smilies/lol.gif",
  ":love:": "smilies/love.gif",
  ":mad:": "smilies/mad.gif",
  ":miam:": "smilies/miam.gif",
  ":mmmfff:": "smilies/mmmfff.gif",
  ":mouais:": "smilies/mouais.gif",
  ":na:": "smilies/na.gif",
  ":non:": "smilies/non.gif",
  ":ouch:": "smilies/ouch.gif",
  ":ouimaitre:": "smilies/ouimaitre.gif",
  ":pfff:": "smilies/pfff.gif",
  ":pouah:": "smilies/pouah.gif",
  ":pt1cable:": "smilies/pt1cable.gif",
  ":sarcastic:": "smilies/sarcastic.gif",
  ":sleep:": "smilies/sleep.gif",
  ":sol:": "smilies/sol.gif",
  ":spamafote:": "smilies/spamafote.gif",
  ":spookie:": "smilies/spookie.gif",
  ":sum:": "smilies/sum.gif",
  ":sweat:": "smilies/sweat.gif",
  ":vomi:": "smilies/vomi.gif",
  ":wahoo:": "smilies/wahoo.gif",
  ":whistle:": "smilies/whistle.gif"
};

let timerGen = null;
let genTime = 250;
let genLast = 0;

let waitImg = new Image();
waitImg.src = "_css/wait.gif";
waitImg.alt = waitImg.title = "waiting...";

let nopeImg = new Image();
nopeImg.src = "_css/nope.png";
nopeImg.alt = nopeImg.title = "nope...";

let $ = function(id) {
  return document.getElementById(id);
}

function getOffsets(el) {
  var _x = 0;
  var _y = 0;
  while(el && !isNaN(el.offsetLeft) && !isNaN(el.offsetTop)) {
    _x += el.offsetLeft /*- el.scrollLeft*/ ;
    _y += el.offsetTop - el.scrollTop;
    el = el.offsetParent;
  }
  return {
    top: _y,
    left: _x
  };
}

function lowerAscii(s) {
  return s.replace(/[A-Z]/g, (match) => {
    return match.toLowerCase();
  });
}

function updateTopMargin() {
  let main = $("main");
  main.style.marginTop = "min(max(calc(100vh - 30px - " + main.offsetHeight +
    "px), 0px), calc((100vh / 3) - 15px))";
}

function updateWait(generateur) {
  let bbcode = $(generateur.id + "_bbcode");
  let preview = $(generateur.id + "_preview");
  preview.replaceChild(waitImg.cloneNode(), preview.firstElementChild);
  bbcode.value = waitImg.alt;
  updateTopMargin();
}

function updateNope(generateur) {
  let bbcode = $(generateur.id + "_bbcode");
  let preview = $(generateur.id + "_preview");
  preview.replaceChild(nopeImg.cloneNode(), preview.firstElementChild);
  bbcode.value = nopeImg.alt;
  updateTopMargin();
}

function updateImg(generateur, url, alt) {
  let bbcode = $(generateur.id + "_bbcode");
  let preview = $(generateur.id + "_preview");
  let img = new Image();
  let lastCall = Date.now();
  generateur.lastCall = lastCall;
  let imgLoad = function() {
    if(lastCall !== generateur.lastCall) return;
    window.clearTimeout(generateur.timerImg);
    img.alt = img.title = alt;
    preview.replaceChild(img, preview.firstElementChild);
    bbcode.value = "[img]https://reho.st/" +
      img.src.replace(/&TIMESTAMP=[0-9]+$/g, "") + "[/img]";
    updateTopMargin();
  };
  let imgError = function() {
    if(lastCall !== generateur.lastCall) return;
    window.clearTimeout(generateur.timerImg);
    updateNope(generateur);
  };
  img.addEventListener("load", imgLoad, false);
  img.addEventListener("error", imgError, false);
  window.clearTimeout(generateur.timerImg);
  generateur.timerImg = window.setTimeout(function() {
    if(lastCall !== generateur.lastCall) return;
    img.removeEventListener("load", imgLoad, false);
    img.removeEventListener("error", imgError, false);
    img.src = "";
    updateNope(generateur);
  }, 30000); // 30 seconds for image timeout
  img.src = url + "&TIMESTAMP=" + lastCall;
}

function getSmileys(generateur, callback) {
  fetch("./_api/smileys.txt", {
    method: "GET",
    mode: "same-origin",
    credentials: "omit",
    cache: "reload",
    referrer: "",
    referrerPolicy: "no-referrer"
  }).then(function(r) {
    return r.text();
  }).then(function(r) {
    let s = r.split("\n");
    s = s.concat(Object.keys(bases));
    callback(generateur, s, s.length);
  }).catch(function(e) {
    console.log("ERROR fetch smileys.txt", generateur.id, e);
    updateNope(generateur);
  });
}

function wheelEvent(event, type, target, generateur, step = 1) {
  if(type === "range") {
    let before = target.valueAsNumber;
    target.valueAsNumber += (event.deltaY < 0) ? step : -step;
    event.preventDefault();
    event.stopPropagation();
    if(target.valueAsNumber !== before) {
      generateurs.initOptions(generateur);
      generateurs.generateImgTimer(generateur);
    }
  }
  if(type === "select") {
    let before = target.selectedIndex;
    target.selectedIndex = (event.deltaY < 0) ?
      Math.max(target.selectedIndex - 1, 0) :
      Math.min(target.selectedIndex + 1, target.childElementCount - 1);
    event.preventDefault();
    event.stopPropagation();
    if(target.selectedIndex !== before) {
      generateurs.generateImgTimer(generateur);
    }
  }
}

window.addEventListener("resize", updateTopMargin, false);

let generateurs = {
  generateurObjs: {
    alerte: {
      id: "alerte",
      label: "Alerte",
      url: "alerte/?t=",
      alt: "Alerte {$1}",

      timerImg: null,
      lastCall: null
    },

    nazi: {
      id: "nazi",
      label: "Nazi",
      url: "nazi/?t=",
      alt: "",

      timerImg: null,
      lastCall: null,

      generateImg: function(generateur) {
        let input = $(generateur.id + "_t").value.toLocaleUpperCase("FR-fr");
        let url = generateur.url + encodeURIComponent(input);
        let alt = input + " NAZI";
        updateImg(generateur, url, alt);
      }
    },

    fb: {
      id: "fb",
      label: "Facebook",
      url: "fb/?",
      alt: "",

      timerImg: null,
      lastCall: null,

      initOptions: function(generateur) {
        if($(generateur.id + "_moi").checked) {
          $(generateur.id + "_t").setAttribute("disabled", "disabled");
          $(generateur.id + "_pluriel").setAttribute("disabled", "disabled");
        } else {
          $(generateur.id + "_t").removeAttribute("disabled");
          $(generateur.id + "_pluriel").removeAttribute("disabled");
        }
      },

      generateImg: function(generateur) {
        let input = $(generateur.id + "_t").value;
        let moi = $(generateur.id + "_moi").checked;
        let pluriel = $(generateur.id + "_pluriel").checked;
        let not = $(generateur.id + "_not").checked;
        let sujet = encodeURIComponent(input);
        let url = generateur.url;
        let alt = "";
        if(moi) {
          url += not ? "not&moi" : "moi";
          alt += not ? "Je n'aime pas ça" : "J'aime ça";
        } else {
          if(pluriel) {
            url += not ? "not&pluriel&t=" + sujet : "pluriel&t=" + sujet;
            alt += not ? input + " n'aiment pas ça" : input + " aiment ça";
          } else {
            url += not ? "not&t=" + sujet : "t=" + sujet;
            alt += not ? input + " n'aime pas ça" : input + " aime ça";
          }
        }
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_moi").addEventListener("click", function() {
          generateurs.initOptions(generateur);
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_pluriel").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_not").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
      }
    },

    seagal: {
      id: "seagal",
      label: "Steven Seagal",
      url: "seagal/?t=",
      alt: "Steven Seagal {$1}",

      timerImg: null,
      lastCall: null
    },

    bulle: {
      id: "bulle",
      label: "Bulle",
      url: "bulle/?t=",
      alt: "",

      timerImg: null,
      lastCall: null,

      initOptions: function(generateur) {
        $(generateur.id + "_vdelta").textContent =
          $(generateur.id + "_delta").value + " px";
      },

      generateImg: function(generateur) {
        let input = $(generateur.id + "_t").value;
        let delta = $(generateur.id + "_delta").value;
        let flip = $(generateur.id + "_flip").checked;
        let police = $(generateur.id + "_police").value;
        let taille = $(generateur.id + "_taille").value;
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let url = generateur.url + encodeURIComponent(input);
        url += "&delta=" + delta;
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        url += "&police=" + police;
        url += "&taille=" + taille;
        if(flip) url += "&flip";
        let alt = "Bulle " + (tsmiley !== "" ? tsmiley + " " : "") + input;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_delta").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_ldelta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_delta"), generateur);
        }, false);
        $(generateur.id + "_vdelta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_delta"), generateur);
        }, false);
        $(generateur.id + "_flip").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_police").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_police").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_lpolice").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_police"), generateur);
        }, false);
        $(generateur.id + "_taille").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_taille").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_ltaille").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_taille"), generateur);
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    moitmoit: {
      id: "moitmoit",
      label: "Moit-Moit",
      url: "moitmoit/?o",
      alt: "",

      timerImg: null,
      lastCall: null,

      generateImg: function(generateur) {
        let value1 = $(generateur.id + "_smiley1").value.trim();
        let base1 = [":", ";"].includes(value1.substring(0, 1));
        let smiley1 = base1 ? value1 : value1.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley1 = "";
        let value2 = $(generateur.id + "_smiley2").value.trim();
        let base2 = [":", ";"].includes(value2.substring(0, 1));
        let smiley2 = base2 ? value2 : value2.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley2 = "";
        let vertical = $(generateur.id + "_vertical").checked;
        let url = generateur.url;
        if(smiley1 !== "") {
          if(base1) {
            url += "&s1=" + encodeURIComponent(smiley1);
            tsmiley1 = smiley1;
          } else {
            let s1 = smiley1.split(":");
            url += "&s1=" + encodeURIComponent(s1[0]);
            if(s1.length > 1) {
              let r1 = parseInt(s1[1], 10);
              if(!isNaN(r1) && r1 >= 1 && r1 <= 10) {
                url += "&r1=" + r1;
              }
            }
            tsmiley1 = "[:" + smiley1 + "]";
          }
        }
        if(smiley2 !== "") {
          if(base2) {
            url += "&s2=" + encodeURIComponent(smiley2);
            tsmiley2 = smiley2;
          } else {
            let s2 = smiley2.split(":");
            url += "&s2=" + encodeURIComponent(s2[0]);
            if(s2.length > 1) {
              let r2 = parseInt(s2[1], 10);
              if(!isNaN(r2) && r2 >= 1 && r2 <= 10) {
                url += "&r2=" + r2;
              }
            }
            tsmiley2 = "[:" + smiley2 + "]";
          }
        }
        if(vertical) url += "&v";
        let alt = "Moit-Moit " + tsmiley1 + " / " + tsmiley2;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_vertical").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_switch").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            let smiley1 = $(generateur.id + "_smiley1");
            let smiley2 = $(generateur.id + "_smiley2");
            let tmp = smiley1.value;
            smiley1.value = smiley2.value;
            smiley2.value = tmp;
            generateurs.generateImg(generateur);
          });
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            getSmileys(generateur, function(generateur, smileys, length) {
              let i1 = Math.floor(Math.random() * length);
              let i2 = Math.floor(Math.random() * length);
              $(generateur.id + "_smiley1").value = smileys[i1];
              $(generateur.id + "_smiley2").value = smileys[i2];
              $(generateur.id + "_vertical").checked = false; // default
              generateurs.generateImg(generateur);
            });
          });
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley1", generateur);
        generateurs.addSmileyHelper(generateur.id + "_smiley2", generateur);
      }
    },

    seal: {
      id: "seal",
      label: "Seal of Quality",
      url: "seal/?t=",
      alt: "Original {$1}, Seal of Quality",

      timerImg: null,
      lastCall: null
    },

    ddr555: {
      id: "ddr555",
      label: "Ddr555",
      url: "ddr555/?a=",
      alt: "",

      timerImg: null,
      lastCall: null,

      generateImg: function(generateur) {
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let angle = $(generateur.id + "_angle").value;
        let tangle = $(generateur.id + "_angle")
          .options[$(generateur.id + "_angle").selectedIndex].textContent;
        let url = generateur.url + angle;
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        let alt = "Ddr555 " + tsmiley + " @ " + tangle;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_angle").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_angle").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_langle").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_angle"), generateur);
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            getSmileys(generateur, function(generateur, smileys, length) {
              let i = Math.floor(Math.random() * length);
              $(generateur.id + "_smiley").value = smileys[i];
              $(generateur.id + "_angle").selectedIndex = 3; // 180° default
              generateurs.generateImg(generateur);
            });
          });
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    note: {
      id: "note",
      label: "Note",
      url: "note/?n=",
      alt: "",

      timerImg: null,
      lastCall: null,

      initOptions: function(generateur) {
        $(generateur.id + "_vnumerateur").textContent =
          $(generateur.id + "_numerateur").value;
        $(generateur.id + "_vdenominateur").textContent =
          $(generateur.id + "_denominateur").value;
      },

      generateImg: function(generateur) {
        let numerateur = $(generateur.id + "_numerateur").value;
        let denominateur = $(generateur.id + "_denominateur").value;
        let url = generateur.url + numerateur + "&d=" + denominateur;
        let alt = "Note " + numerateur + " / " + denominateur;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_numerateur").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_numerateur").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_numerateur").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_numerateur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lnumerateur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_numerateur"), generateur);
        }, false);
        $(generateur.id + "_vnumerateur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_numerateur"), generateur);
        }, false);
        $(generateur.id + "_denominateur").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_denominateur").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_denominateur").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_denominateur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_ldenominateur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_denominateur"), generateur);
        }, false);
        $(generateur.id + "_vdenominateur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_denominateur"), generateur);
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            let d = Math.floor(Math.random() * 99);
            let n = Math.floor(Math.random() * d);
            $(generateur.id + "_numerateur").value = n;
            $(generateur.id + "_denominateur").value = d;
            generateurs.initOptions(generateur);
            generateurs.generateImg(generateur);
          });
        }, false);
      }
    },

    rofl: {
      id: "rofl",
      label: "Rofl",
      url: "rofl/?v=",
      alt: "",

      timerImg: null,
      lastCall: null,

      initOptions: function(generateur) {
        $(generateur.id + "_vtours").textContent =
          $(generateur.id + "_tours").value;
        $(generateur.id + "_vdelta").textContent =
          $(generateur.id + "_delta").value + " px";
        $(generateur.id + "_vvitesse").textContent =
          $(generateur.id + "_vitesse").value;
      },

      generateImg: function(generateur) {
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let tours = $(generateur.id + "_tours").value;
        let mode = $(generateur.id + "_mode").value;
        let delta = $(generateur.id + "_delta").value;
        let vitesse = $(generateur.id + "_vitesse").value;
        let url = generateur.url + vitesse;
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        url += "&t=" + tours;
        url += "&m=" + mode;
        url += "&dx=" + delta;
        let alt = "Rofl " + tsmiley;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_tours").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_tours").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_tours").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_tours").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_ltours").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_tours"), generateur);
        }, false);
        $(generateur.id + "_vtours").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_tours"), generateur);
        }, false);
        $(generateur.id + "_mode").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_mode").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_lmode").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_mode"), generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_ldelta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_delta"), generateur);
        }, false);
        $(generateur.id + "_vdelta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_delta"), generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lvitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_vitesse"), generateur);
        }, false);
        $(generateur.id + "_vvitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_vitesse"), generateur);
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            getSmileys(generateur, function(generateur, smileys, length) {
              let i = Math.floor(Math.random() * length);
              $(generateur.id + "_smiley").value = smileys[i];
              $(generateur.id + "_tours").value = 2; // default
              $(generateur.id + "_mode").selectedIndex = 2; // rond 3 default
              $(generateur.id + "_delta").value = 0; // default
              $(generateur.id + "_vitesse").value = 6; // default
              generateurs.initOptions(generateur);
              generateurs.generateImg(generateur);
            });
          });
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    boing: {
      id: "boing",
      label: "Boing",
      url: "boing/?v=",
      alt: "",

      timerImg: null,
      lastCall: null,

      initOptions: function(generateur) {
        $(generateur.id + "_vdelta").textContent =
          $(generateur.id + "_delta").value + " px";
        $(generateur.id + "_vfacteur").textContent =
          $(generateur.id + "_facteur").value;
        $(generateur.id + "_vvitesse").textContent =
          $(generateur.id + "_vitesse").value;
      },

      generateImg: function(generateur) {
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let dx = $(generateur.id + "_delta").value;
        let fy = $(generateur.id + "_facteur").value;
        let rofl = $(generateur.id + "_rofl").checked;
        let vitesse = $(generateur.id + "_vitesse").value;
        let url = generateur.url + vitesse;
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        url += "&dx=" + dx;
        url += "&fy=" + fy;
        if(rofl) url += "&rofl";
        let alt = "Boing " + (rofl ? "rofl " : "") + tsmiley;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_delta").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_delta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_ldelta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_delta"), generateur);
        }, false);
        $(generateur.id + "_vdelta").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_delta"), generateur);
        }, false);
        $(generateur.id + "_facteur").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_facteur").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_facteur").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_facteur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lfacteur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_facteur"), generateur);
        }, false);
        $(generateur.id + "_vfacteur").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_facteur"), generateur);
        }, false);
        $(generateur.id + "_rofl").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lvitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_vitesse"), generateur);
        }, false);
        $(generateur.id + "_vvitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_vitesse"), generateur);
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            getSmileys(generateur, function(generateur, smileys, length) {
              let i = Math.floor(Math.random() * length);
              $(generateur.id + "_smiley").value = smileys[i];
              $(generateur.id + "_facteur").value = 2; // default
              $(generateur.id + "_delta").value = 10; // default
              $(generateur.id + "_rofl").checked = false; // default
              $(generateur.id + "_vitesse").value = 6; // default
              generateurs.initOptions(generateur);
              generateurs.generateImg(generateur);
            });
          });
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    miroir: {
      id: "miroir",
      label: "Miroir",
      url: "miroir/?",
      alt: "",

      timerImg: null,
      lastCall: null,

      generateImg: function(generateur) {
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let part = $(generateur.id + "_part").value;
        let tpart = $(generateur.id + "_part")
          .options[$(generateur.id + "_part").selectedIndex].textContent;
        let url = generateur.url + part
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        let alt = "Miroir " + tpart + " de " + tsmiley;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_part").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_part").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_lpart").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_part"), generateur);
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            getSmileys(generateur, function(generateur, smileys, length) {
              let i = Math.floor(Math.random() * length);
              $(generateur.id + "_smiley").value = smileys[i];
              $(generateur.id + "_part").selectedIndex = 0; // de gauche default
              generateurs.generateImg(generateur);
            });
          });
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    southpark: {
      id: "southpark",
      label: "South Park",
      url: "southpark/?l=",
      alt: "",

      timerImg: null,
      lastCall: null,

      initOptions: function(generateur) {
        if($(generateur.id + "_lim").value === "0") {
          $(generateur.id + "_vlim").textContent = "auto";
        } else {
          $(generateur.id + "_vlim").textContent =
            $(generateur.id + "_lim").value + " px";
        }
      },

      generateImg: function(generateur) {
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let lim = $(generateur.id + "_lim").value;
        let url = generateur.url + lim;
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        let alt = "South Park " + tsmiley;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_lim").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_lim").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_lim").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_lim").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_llim").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_lim"), generateur);
        }, false);
        $(generateur.id + "_vlim").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_lim"), generateur);
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            getSmileys(generateur, function(generateur, smileys, length) {
              let i = Math.floor(Math.random() * length);
              $(generateur.id + "_smiley").value = smileys[i];
              $(generateur.id + "_lim").value = 0; // auto default
              generateurs.initOptions(generateur);
              generateurs.generateImg(generateur);
            });
          });
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    modo: {
      id: "modo",
      label: "Modération",
      url: "modo/?t=",
      alt: "",

      timerImg: null,
      lastCall: null,

      generateImg: function(generateur) {
        let input = $(generateur.id + "_t").value;
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let icons = $(generateur.id + "_icons").value;
        let url = generateur.url + encodeURIComponent(input);
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        url += "&i=" + icons;
        let alt = "La modération a dit : " + input + (tsmiley ? " " + tsmiley : "");
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_icons").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_icons").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_licons").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_icons"), generateur);
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    golden: {
      id: "golden",
      label: "Golden",
      url: "golden/?v=",
      alt: "",

      timerImg: null,
      lastCall: null,

      initOptions: function(generateur) {
        $(generateur.id + "_vstars").textContent =
          $(generateur.id + "_stars").value;
        $(generateur.id + "_vgolden").textContent =
          $(generateur.id + "_golden").value + " %";
        $(generateur.id + "_vframes").textContent =
          $(generateur.id + "_frames").value;
        $(generateur.id + "_vvitesse").textContent =
          $(generateur.id + "_vitesse").value;
      },

      generateImg: function(generateur) {
        let value = $(generateur.id + "_smiley").value.trim();
        let base = [":", ";"].includes(value.substring(0, 1));
        let smiley = base ? value : value.replace(/^[\[:]+|[:\]]+$/g, "").trim();
        let tsmiley = "";
        let stars = $(generateur.id + "_stars").value;
        let golden = $(generateur.id + "_golden").value;
        let position = $(generateur.id + "_position").value;
        let taille = $(generateur.id + "_taille").value;
        let frames = $(generateur.id + "_frames").value;
        let vitesse = $(generateur.id + "_vitesse").value;
        let url = generateur.url + vitesse;
        if(smiley !== "") {
          if(base) {
            url += "&s=" + encodeURIComponent(smiley);
            tsmiley = smiley;
          } else {
            let s = smiley.split(":");
            url += "&s=" + encodeURIComponent(s[0].trim());
            if(s.length > 1) {
              let r = parseInt(s[1].trim(), 10);
              if(!isNaN(r) && r >= 1 && r <= 10) {
                url += "&r=" + r;
              }
            }
            tsmiley = "[:" + smiley + "]";
          }
        }
        url += "&stars=" + stars;
        url += "&golden=" + golden;
        switch(position) {
          case "WO":
            url += "&wo";
            break;
          case "HG":
            url += "&av=H&ah=G";
            break;
          case "HD":
            url += "&av=H&ah=D";
            break;
          case "BG":
            url += "&av=B&ah=G";
            break;
          case "BD":
            url += "&av=B&ah=D";
            break;
          case "VHG":
            url += "&av=H&ah=G&vert";
            break;
          case "VHD":
            url += "&av=H&ah=D&vert";
            break;
          case "VBG":
            url += "&av=B&ah=G&vert";
            break;
          case "VBD":
            url += "&av=B&ah=D&vert";
            break;
        }
        url += "&taille=" + taille;
        url += "&frames=" + frames;
        let alt = "Golden " + tsmiley;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_stars").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_stars").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_stars").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_stars").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lstars").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_stars"), generateur);
        }, false);
        $(generateur.id + "_vstars").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_stars"), generateur);
        }, false);
        $(generateur.id + "_golden").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_golden").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_golden").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_golden").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lgolden").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_golden"), generateur);
        }, false);
        $(generateur.id + "_vgolden").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_golden"), generateur);
        }, false);
        $(generateur.id + "_position").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_position").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_lposition").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_position"), generateur);
        }, false);
        $(generateur.id + "_taille").addEventListener("change", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_taille").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", this, generateur);
        }, false);
        $(generateur.id + "_ltaille").addEventListener("wheel", function(event) {
          wheelEvent(event, "select", $(generateur.id + "_taille"), generateur);
        }, false);
        $(generateur.id + "_frames").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_frames").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_frames").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_frames").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lframes").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_frames"), generateur);
        }, false);
        $(generateur.id + "_vframes").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_frames"), generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("input", function() {
          generateurs.initOptions(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("mouseup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_vitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", this, generateur);
        }, false);
        $(generateur.id + "_lvitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_vitesse"), generateur);
        }, false);
        $(generateur.id + "_vvitesse").addEventListener("wheel", function(event) {
          wheelEvent(event, "range", $(generateur.id + "_vitesse"), generateur);
        }, false);
        $(generateur.id + "_random").addEventListener("click", function() {
          generateurs.generateImgTimer(generateur, function(generateur) {
            getSmileys(generateur, function(generateur, smileys, length) {
              let i = Math.floor(Math.random() * length);
              $(generateur.id + "_smiley").value = smileys[i];
              $(generateur.id + "_stars").value = 5; // default
              $(generateur.id + "_golden").value = 50; // default
              // horizontal en bas à gauche default
              $(generateur.id + "_position").selectedIndex = 3;
              $(generateur.id + "_taille").selectedIndex = 0; // 1 default
              $(generateur.id + "_frames").value = 20; // default
              $(generateur.id + "_vitesse").value = 8; // default
              generateurs.initOptions(generateur);
              generateurs.generateImg(generateur);
            });
          });
        }, false);
        generateurs.addSmileyHelper(generateur.id + "_smiley", generateur);
      }
    },

    ump: {
      id: "ump",
      label: "UMP",
      url: "ump/?t=",
      alt: "UMP {$1}",

      timerImg: null,
      lastCall: null
    },

    bfmtv: {
      id: "bfmtv",
      label: "BFMTV",
      url: "bfmtv/?o",
      alt: "",

      timerImg: null,
      lastCall: null,

      generateImg: function(generateur) {
        let text1 = $(generateur.id + "_text1").value;
        let text2 = $(generateur.id + "_text2").value;
        let smiley = $(generateur.id + "_s").checked;
        let url = generateur.url;
        if(text1 !== "") url += "&t1=" + encodeURIComponent(text1);
        if(text2 !== "") url += "&t2=" + encodeURIComponent(text2);
        if(smiley) url += "&s";
        let alt = "BFMTV " + text1 + "\n" + text2;
        updateImg(generateur, url, alt);
      },

      addHandler: function(generateur) {
        generateurs.defaultAddHandler(generateur);
        $(generateur.id + "_text1").addEventListener("paste", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_text1").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_text2").addEventListener("paste", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
        $(generateur.id + "_text2").addEventListener("keyup", function() {
          generateurs.generateImgTimer(generateur);
        }, false);
      }
    }
  },

  addSmileyHelper: function(smileyId, generateur) {
    let smiley = $(smileyId);
    let oldValue = "";
    let lastValue = "";
    let lastText = "";
    let timerKey = null;
    let keyTime = 100;
    let keyLast = 0;
    let timerBlur = null;
    let blurTime = 100;

    let smileyHelper = document.createElement("div");
    smileyHelper.className = "smiley_helper";
    let select = document.createElement("select");
    select.className = "smiley_helper";
    select.size = 8;
    smileyHelper.appendChild(select);
    document.body.appendChild(smileyHelper);

    let clearSmileyHelper = function() {
      if(select.nextElementSibling) {
        smileyHelper.removeChild(select.nextElementSibling);
      }
      while(select.firstElementChild) {
        select.removeChild(select.firstElementChild);
      }
    }

    let hideSmileyHelper = function() {
      smileyHelper.style.display = "none";
      if(select.nextElementSibling) {
        smileyHelper.removeChild(select.nextElementSibling);
      }
    }

    let startHideSmileyHelper = function() {
      window.clearTimeout(timerBlur);
      timerBlur = window.setTimeout(hideSmileyHelper, blurTime);
    }

    let displaySmileyHelper = function() {
      let smileyOffset = getOffsets(smiley);
      smileyHelper.style.top = (smileyOffset.top + 35) + "px";
      smileyHelper.style.left = smileyOffset.left + "px";
      smileyHelper.style.display = "flex";
    }

    let generatePreview = function() {
      if(smiley.value !== select.value) {
        smiley.value = select.value;
        generateurs.generateImgTimer(generateur);
      }
      let url, alt;
      if(typeof bases[select.value] !== "undefined") {
        url = "https://forum-images.hardware.fr/icones/" + bases[select.value];
        alt = select.value;
      } else {
        let value = select.value.split(":");
        let code = value[0];
        let rang = value.length > 1 ? value[1] : 0;
        url = "https://forum-images.hardware.fr/images/perso/";
        if(rang) {
          url += rang + "/";
        }
        url += code + ".gif";
        alt = "[:" + code + (rang ? ":" + rang : "") + "]";
      }
      let preview = select.nextElementSibling ? select.nextElementSibling :
        smileyHelper.appendChild(document.createElement("img"));
      preview.src = url;
      preview.alt = preview.title = alt;
    }

    let startGeneratePreview = function() {
      window.clearTimeout(timerKey);
      let lastCall = Date.now();
      let waitTime = Math.max(keyTime - (lastCall - keyLast), 0);
      keyLast = lastCall;
      timerKey = window.setTimeout(generatePreview, waitTime);
    }

    let updateSmileyHelper = function() {
      let value = lowerAscii(smiley.value.trim());
      let base = [":", ";"].includes(value.substring(0, 1));
      // dont remove the trailing colon here for search
      let text = value.replace(/^[\[:]+|[\]]+$/g, "").trim();
      if(base) {
        if(value !== lastValue) {
          lastValue = value;
          clearSmileyHelper();
          for(const smiley in bases) {
            if(lowerAscii(smiley).startsWith(value)) {
              let option = document.createElement("option");
              option.value = smiley;
              option.textContent = smiley;
              select.appendChild(option);
            }
          }
        }
        displaySmileyHelper();
      } else if(text.length >= 2) {
        if(text !== lastText) {
          lastText = text;
          fetch("./_api/get_by_name.php?pattern=" + encodeURIComponent(text), {
            method: "GET",
            mode: "same-origin",
            credentials: "omit",
            cache: "reload",
            referrer: "",
            referrerPolicy: "no-referrer"
          }).then(function(r) {
            return r.text();
          }).then(function(r) {
            clearSmileyHelper();
            let smileys = r.trim();
            if(smileys !== "") {
              smileys = smileys.split(";");
              smileys.forEach(function(smiley) {
                smiley = smiley.trim();
                if(smiley !== "") {
                  let option = document.createElement("option");
                  option.value = smiley;
                  option.textContent = smiley;
                  select.appendChild(option);
                }
              });
            }
            displaySmileyHelper();
          }).catch(function(e) {
            console.log("ERROR fetch get_by_name.php", text, e);
          });
        } else {
          displaySmileyHelper();
        }
      } else {
        hideSmileyHelper();
      }
    }

    let startUpdateSmileyHelper = function() {
      window.clearTimeout(timerKey);
      let lastCall = Date.now();
      let waitTime = Math.max(keyTime - (lastCall - keyLast), 0);
      keyLast = lastCall;
      timerKey = window.setTimeout(updateSmileyHelper, waitTime);
    }

    smiley.addEventListener("blur", function() {
      startHideSmileyHelper();
    }, false);

    smiley.addEventListener("focus", function() {
      window.clearTimeout(timerBlur);
    }, false);

    smiley.addEventListener("click", function() {
      oldValue = smiley.value;
      startUpdateSmileyHelper();
    }, false);

    smiley.addEventListener("paste", function() {
      oldValue = smiley.value;
      startUpdateSmileyHelper();
      generateurs.generateImgTimer(generateur);
    }, false);

    smiley.addEventListener("keydown", function(event) {
      if(event.key === "Tab" ||
        event.key === "ArrowDown" ||
        event.key === "ArrowUp" ||
        event.key === "Escape" ||
        event.key === "Enter") {
        event.preventDefault();
      }
      oldValue = smiley.value;
      if(smileyHelper.style.display !== "none" &&
        select.childElementCount) {
        if(event.key === "Tab" ||
          event.key === "ArrowDown") {
          select.focus();
          select.selectedIndex = 0;
          startGeneratePreview();
        }
        if(event.key === "ArrowUp") {
          select.focus();
          select.selectedIndex = select.childElementCount - 1;
          startGeneratePreview();
        }
      }
    }, false);

    smiley.addEventListener("keyup", function(event) {
      if(event.key === "Tab" ||
        event.key === "ArrowDown" ||
        event.key === "ArrowUp" ||
        event.key === "Escape" ||
        event.key === "Enter") {
        event.preventDefault();
      }
      if(smileyHelper.style.display !== "none") {
        if(event.key === "Escape") {
          hideSmileyHelper();
          return;
        }
        if(event.key === "Enter") {
          if(select.childElementCount === 1) {
            select.selectedIndex = 0;
            smiley.value = select.value;
            generateurs.generateImgTimer(generateur);
            hideSmileyHelper();
          }
          if(select.childElementCount > 1) {
            select.focus();
            select.selectedIndex = 0;
            startGeneratePreview();
          }
          return;
        }
      }
      startUpdateSmileyHelper();
      generateurs.generateImgTimer(generateur);
    }, false);

    select.addEventListener("blur", function() {
      startHideSmileyHelper();
    }, false);

    select.addEventListener("focus", function() {
      window.clearTimeout(timerBlur);
    }, false);

    select.addEventListener("click", function() {
      if(select.childElementCount) {
        if(select.selectedIndex === -1) {
          select.selectedIndex = 0;
        }
        startGeneratePreview();
      }
    }, false);

    select.addEventListener("keydown", function(event) {
      if(event.key === "Tab" ||
        event.key === "ArrowRight" ||
        event.key === "ArrowDown" ||
        event.key === "ArrowLeft" ||
        event.key === "ArrowUp" ||
        event.key === "Escape" ||
        event.key === "Enter" ||
        event.key === " " ||
        event.key === "Backspace") {
        event.preventDefault();
      }
      if(select.childElementCount) {
        if(event.key === "Tab" ||
          event.key === "ArrowRight" ||
          event.key === "ArrowDown") {
          select.selectedIndex =
            select.selectedIndex + 1 === select.childElementCount ?
            0 : select.selectedIndex + 1;
          startGeneratePreview();
        }
        if(event.key === "ArrowLeft" ||
          event.key === "ArrowUp") {
          select.selectedIndex =
            select.selectedIndex - 1 === -1 ?
            select.childElementCount - 1 : select.selectedIndex - 1;
          startGeneratePreview();
        }
      }
    }, false);

    select.addEventListener("keyup", function(event) {
      if(event.key === "Tab" ||
        event.key === "ArrowRight" ||
        event.key === "ArrowDown" ||
        event.key === "ArrowLeft" ||
        event.key === "ArrowUp" ||
        event.key === "Escape" ||
        event.key === "Enter" ||
        event.key === " " ||
        event.key === "Backspace") {
        event.preventDefault();
      }
      if(event.key === "Escape" ||
        event.key === "Backspace" ||
        (select.selectedIndex !== -1 &&
          (event.key === "Enter" ||
            event.key === " "))) {
        if(event.key === "Escape" ||
          event.key === "Backspace") {
          smiley.value = oldValue;
          generateurs.generateImgTimer(generateur);
        }
        hideSmileyHelper();
        smiley.focus();
      }
    }, false);
  },

  launch: function() {
    for(let generateur in generateurs.generateurObjs) {
      generateurs.initOptions(generateurs.generateurObjs[generateur]);
      generateurs.addHandler(generateurs.generateurObjs[generateur]);
      generateurs.generateImg(generateurs.generateurObjs[generateur]);
    }
    generateurs.generateMenu();
    generateurs.selectGenerateur(generateurs.readCookie("generateur"));
  },

  initOptions: function(generateur) {
    let initOptions = generateur.initOptions ?
      generateur.initOptions : generateurs.defaultInitOptions;
    initOptions(generateur);
  },

  defaultInitOptions: function(generateur) {},

  addHandler: function(generateur) {
    let addHandler = generateur.addHandler ?
      generateur.addHandler : generateurs.defaultAddHandler;
    addHandler(generateur);
  },

  defaultAddHandler: function(generateur) {
    $(generateur.id + "_bbcode").addEventListener("focus", function() {
      this.select();
    }, false);
    $(generateur.id + "_t")?.addEventListener("paste", function() {
      generateurs.generateImgTimer(generateur);
    }, false);
    $(generateur.id + "_t")?.addEventListener("keyup", function() {
      generateurs.generateImgTimer(generateur);
    }, false);
    $(generateur.id + "_s")?.addEventListener("click", function() {
      generateurs.generateImgTimer(generateur);
    }, false);
  },

  generateImgTimer: function(generateur, custom) {
    updateWait(generateur);
    window.clearTimeout(timerGen);
    let lastCall = Date.now();
    let waitTime = Math.max(genTime - (lastCall - genLast), 0);
    genLast = lastCall;
    timerGen = window.setTimeout(function() {
      custom ? custom(generateur) : generateurs.generateImg(generateur);
    }, waitTime);
  },

  generateImg: function(generateur) {
    let generateImg = generateur.generateImg ?
      generateur.generateImg : generateurs.defaultGenerateImg;
    generateImg(generateur);
  },

  defaultGenerateImg: function(generateur) {
    let input = $(generateur.id + "_t").value;
    let smiley = $(generateur.id + "_s")?.checked;
    let url = generateur.url + encodeURIComponent(input);
    if(smiley) url += "&s";
    let alt = generateur.alt.replace("{$1}", input);
    updateImg(generateur, url, alt);
  },

  generateMenu: function() {
    let menu = $("menu");
    for(let g in generateurs.generateurObjs) {
      let id = generateurs.generateurObjs[g].id;
      let label = generateurs.generateurObjs[g].label;
      let button = document.createElement("input");
      button.setAttribute("type", "button");
      button.setAttribute("id", id + "_menu");
      button.setAttribute("value", label);
      button.addEventListener("click", function() {
        generateurs.selectGenerateur(id);
      }, false);
      menu.appendChild(button);
    }
  },

  selectGenerateur: function(generateurId) {
    if(generateurId) {
      generateurs.writeCookie("generateur", generateurId);
    }
    for(let g in generateurs.generateurObjs) {
      let id = generateurs.generateurObjs[g].id;
      let button = $(id + "_menu");
      let div = $(id + "_div");
      button.classList.toggle("selected", id === generateurId);
      div.style.display = (id === generateurId) ? "flex" : "none";
    }
    updateTopMargin();
  },

  writeCookie: function(data, value) {
    let date = new Date;
    date.setMonth(date.getMonth() + 1);
    document.cookie = data + "=" + encodeURIComponent(value) +
      "; expires=" + date.toUTCString() + "; samesite=lax";
  },

  readCookie: function(data) {
    return document.cookie.split("; ")
      .find((r) => r.startsWith(data + "="))?.split("=")[1];
  }
};