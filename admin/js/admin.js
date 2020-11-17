/*début du document ready*****************************/

'use strict';

$(document).ready(function () {
  /*permet le déroulement des messages contacts*/
  $('#form_contact textarea').click(function () {
    $(this).removeAttr('id');
  });

  $(document).on('click', '.deroul', function () {
    if ($(this).next().find('div').is(':visible')) {
      $(this).next().css({
        'background-color': 'rgb(200,200,200)',
        'transition': 'background-color .3s'
      });
      $(this).next().find('div').slideToggle();
    } else {
      $(this).next().css({
        'background-color': 'rgb(240,240,240)',
        'transition': 'background-color .3s'
      });
      $(this).next().find('div').slideToggle();
    }
  });


  $('#droite').css('min-height', ($('#gauche ul').height() * 1.3) + 'px');
  $('#btn_centre').height($('#btn_centre').width());

  $(window).resize(function () {
    $('#btn_centre').height($('#btn_centre').width());
  });


  if ($('#form_comptes').length > 0) {
    $('input[type="file"]').customFileInput();
  }
  if ($('#form_dossiers').length > 0) {
    $('input[type="file"]').customFileInput();
  }
  if ($('#form_documents').length > 0) {
    $('input[type="file"]').customFileInput({
      buttonText: 'Sélectionnez votre fichier (pdf uniquement)'
    });
  }
  if ($('#form_evenement').length > 0) {
    $('input[type="file"]').customFileInput({
      buttonText: 'Sélectionnez votre image à la une (png | jpg | gif) (facultatif)'
    });
  }
  if ($('#form_liens').length > 0) {
    $('input[type="file"]').customFileInput();
  }
  if ($('#form_question').length > 0) {
    $('input[type="file"]').customFileInput();
  }
  if ($('#form_adherents').length > 0) {
    $('input[type="file"]').customFileInput();
  }
  if ($('#form_candidats').length > 0) {
    $('#file1').customFileInput();
    $('#file2').customFileInput();
  }
  if ($('#form_offres').length > 0) {
    $('input[type="file"]').customFileInput();
  }
  if ($('#form_parametre').length > 0) {
    $('#file1').customFileInput();
    $('#file2').customFileInput();
  }

  if ($('#form_css').length > 0) {
    $('input[type="file"]').customFileInput({
      buttonText: 'Fichier à uploader (.css)'
    });
  }

  if ($('#form_contacts_news').length > 0) {
    $('input[type="file"]').customFileInput({
      buttonText: 'Fichier csv à uploader (séparateur : ";")'
    });
  }

  if ($('#form_evenement').length > 0) {
    choix_langue_pages('#droite form select[name="id_langue_evenement"]');
  }

  if ($('#form_actu').length > 0) {
    choix_langue_pages('#droite form select[name="id_langue_actu"]');
  }

  if ($('#form_medias').length > 0) {
    $('input[type="file"]').customFileInput({
      buttonText: 'Sélectionnez votre fichier (formats : svg | png | gif | jpg | pdf | mp4)'
    });
  }

  if ($('#form_pages').length > 0) {
    choix_langue_rubriques('#droite form select[name="id_langue_page"]');
    form_page();
  }

  if ($('#form_edit_css').length > 0) {
    var editor_css = CodeMirror.fromTextArea(document.getElementById("code-css"), {
      lineNumbers: true,
      mode: "css",
      gutters: ["CodeMirror-lint-markers"],
      lint: true
    });
  }

});



/*================================================================================*/

function choix_langue_rubriques(select) {
  if ($(select).val() != '') {
    requete_rubriques($(select).val());
  }

  $(select).change(function () {
    $('body').append('<div id="loader"><div></div></div>');
    requete_rubriques($(this).val());
  });
};


function choix_langue_pages(select) {
  if ($(select).val() != '') {
    requete_pages($(select).val());
  }

  $(select).change(function () {
    $('body').append('<div id="loader"><div></div></div>');
    requete_pages($(this).val());
  });
};

function requete_rubriques(id_langue_page) {
  $.ajax({
    type: 'POST',
    url: '../admin/ajax.php',
    data: 'id_langue_page=' + id_langue_page,
    success: function (reponse) {
      $('#droite form select[name="id_rubrique"]').html(reponse);
      if ($('#droite form select[name="id_langue_page"]').val() == '') {
        $('#droite form select[name="id_rubrique"]')
          .html('<option value="">Sélectionner la rubrique</option>');
      }
      $('#loader').remove();
    }
  });
};

function requete_pages(id_langue_evenement) {
  $.ajax({
    type: 'POST',
    url: '../admin/ajax.php',
    data: 'id_langue_evenement=' + id_langue_evenement,
    success: function (reponse) {
      $('#droite form select[name="id_page"]').html(reponse);
      if ($('#droite form select[name="id_langue_evenement"]').val() == '') {
        $('#droite form select[name="id_page"]')
          .html('<option value="">Sélectionner la page à associer (facultatif)</option>');
      }
      $('#loader').remove();
    }
  });
};


/*------------------------------------------------------------------------------------------------------------*/


$.fn.customFileInput = function (options) {
  var defaults = {
    width: 'inherit',
    buttonText: 'Sélectionnez votre fichier',
    changeText: 'change',
    inputText: 'No file selected',
    icone: '',
    cut_fichier: 30,
    showInputText: true,
    maxFileSize: 0,
    onChange: $.noop
  };
  var opts = $.extend(true, {}, defaults, options);

  var fileInput = $(this)
    .addClass('customfile-input')
    .mouseover(function () {
      upload.addClass('customfile-hover');
    })
    .mouseout(function () {
      upload.removeClass('customfile-hover');
    })
    .focus(function () {
      upload.addClass('customfile-focus');
      fileInput.data('val', fileInput.val());
    })
    .blur(function () {
      upload.removeClass('customfile-focus');
      $(this).trigger('checkChange');
    })
    .bind('disable', function () {
      fileInput.attr('disabled', true);
      upload.addClass('customfile-disabled');
    })
    .bind('enable', function () {
      fileInput.removeAttr('disabled');
      upload.removeClass('customfile-disabled');
    })
    .bind('checkChange', function () {
      if (fileInput.val() && fileInput.val() != fileInput.data('val')) {
        fileInput.trigger('change');
      }
    })
    .mousedown(function (e) {
      if (e.button == 2) {
        fileInput.css({
          'top': e.pageY - upload.offset().top - fileInput.outerHeight() - 30
        });
      }
      return true;
    })
    .bind('change', function () {

      var fileName = $(this).val().split(/\\/).pop();
      var cut_fileName = fileName.substr(0, opts.cut_fichier) + '...';

      var fileExt = 'customfile-ext-' + fileName.split('.').pop().toLowerCase();


      uploadFeedback
        .html(cut_fileName + '<span>Parcourir</span>')
        .removeClass(uploadFeedback.data('fileExt') || '')
        .addClass(fileExt)
        .data('fileExt', fileExt)
        .addClass('customfile-feedback-populated');

      /* uploadButton.text('Change'); */
    })
    .click(function () {
      fileInput.data('val', fileInput.val());
      setTimeout(function () {
        fileInput.trigger('checkChange');
      }, 100);
    });

  var upload = $('<div class="customfile"></div>');
  var uploadFeedback = $('<a href="javascript:void(0)" class="customfile-feedback" aria-hidden="true"></a>')
    .html(opts.buttonText + '<span>Parcourir</span>')
    .css({
      'background-image': 'url(' + opts.icone + ')',
      'background-repeat': 'no-repeat',
      'background-position': 'left center'
    })
    .appendTo(upload);

  if (fileInput.is('[disabled]')) {
    fileInput.trigger('disable');
  }
  upload
    .mousemove(function (e) {
      fileInput.css({
        'left': e.pageX - upload.offset().left - fileInput.outerWidth() + 20,
        'top': e.pageY - upload.offset().top - fileInput.outerHeight() + 10
      });
    })
    .insertAfter(fileInput);

  fileInput.appendTo(upload);

  return $(this);
};



/* FONCTION DROPDOWN DOSSIERS */

function dossierDropdown() {
  var dossiers = document.getElementsByClassName('dossier');
  if (dossiers) {
    for (var i = 0; i < dossiers.length; i++) {
      var d = dossiers[i];
      var first = d.querySelector('tr:first-child');
      if (!first) {
        return;
      }
      var s = window.getComputedStyle(first, null);

      var closeH = s.height;
      var table = d.getElementsByTagName('table');
      if (!table) {
        return;
      }
      var s2 = window.getComputedStyle(table[0], null);
      var openH = s2.height;
      d.id = 'dossier-' + i;
      first.setAttribute('data-target', 'dossier-' + i);
      d.style.overflowY = 'hidden';
      d.style.height = closeH;
      d.setAttribute('data-state', 'close');
      d.style.transition = 'all .3s';
      d.setAttribute('data-close', closeH);
      d.setAttribute('data-open', openH);

      first.style.cursor = 'pointer';
      first.addEventListener('click', function (e) {
        e.preventDefault();
        var target = this.getAttribute('data-target');
        var target = document.getElementById(target);
        var state = target.getAttribute('data-state');
        if (state == 'open') {
          target.style.height = target.getAttribute('data-close');
          target.setAttribute('data-state', 'close');
        } else {
          target.style.height = target.getAttribute('data-open');
          target.setAttribute('data-state', 'open');
        }
      });
    }
  }
}


/* YSeditor ================================================================== */

function YSeditor(selector) {
  var editors = document.querySelectorAll(selector);
  if (editors) {
    this.editors = [];
    for (var i = 0; i < editors.length; i++) {
      if (editors[i].className.match('gallery') != null) {
        new YSeditorGallery(editors[i]);
      } else if (editors[i].className.match('pdf') != null) {
        new YSeditorPDF(editors[i]);
      } else {
        var editor = new YSeditorObject(editors[i]);
        this.editors.push(editor);
      }
    }
  }
};

YSeditor.prototype = {
  addColor: function (colors) {
    for (var i = 0; i < this.editors.length; i++) {
      this.editors[i].addColor(colors);
    }
  },
  addFont: function (name) {
    for (var i = 0; i < this.editors.length; i++) {
      this.editors[i].doc.style.fontFamily = name;
    }
  }
};

function YSeditorGallery(editor) {
  var t = this;
  t.container = editor;
  t.seeHTML = false;
  var textarea = editor.querySelector('textarea');
  if (textarea) {
    textarea.style.display = 'none';
    textarea.style.fontFamily = 'monotype';
    t.textarea = textarea;
  }
  var iframe = document.createElement('div');
  iframe.style.minHeight = '300px';
  iframe.style.width = '100%';
  iframe.style.backgroundColor = '#fff';
  iframe.contentEditable = true;
  iframe.className = 'gallery';
  var value = t.textarea.value;
  t.doc = iframe;
  if (value != '') {
    var newVal = '';
    var ids = value.split(';');
    for (var nb = 0; nb < ids.length; nb++) {
      if (ids[nb] != '') {
        var ar = ids[nb].split('.');
        t.doc.innerHTML += '<img src="../medias/media' + ar[0] + '_p.' + ar[1] + '">';
        newVal += ar[0] + ';';
      }
    }
    t.textarea.value = newVal;
  }
  editor.appendChild(iframe);
  t.observe();
};

YSeditorGallery.prototype = {
  observe() {
    var t = this;
    t.doc.addEventListener('keydown', function (e) {
      e.preventDefault();
    });

    t.doc.addEventListener('click', (e) => {
      if (e.target.tagName == 'IMG') {
        var data = e.target.src.match(/media([0-9]+)/i)[1];
        t.textarea.value = t.textarea.value.replace(data + ';', '');
        e.target.remove();
      }
    });

    t.doc.addEventListener('drop', (e) => {
      e.preventDefault();
      var originalData = e.dataTransfer.getData('URL');
      if (originalData && originalData != '' && originalData.match(/media([0-9]+)/i)) {
        var data = originalData.match(/media([0-9]+)/i)[1];
        var ext = originalData.split('.');
        ext = ext[ext.length - 1];
        t.textarea.value = (t.textarea.value != '') ? t.textarea.value + data + ';' : data + ';';
        t.doc.innerHTML = t.doc.innerHTML + '<img src="../medias/media' + data + '_p.' + ext + '">';
      }
    });
  }
};

function YSeditorPDF(editor) {
  var t = this;
  t.container = editor;
  t.seeHTML = false;
  var textarea = editor.querySelector('textarea');
  if (textarea) {
    textarea.style.display = 'none';
    textarea.style.fontFamily = 'monotype';
    t.textarea = textarea;
  }
  var iframe = document.createElement('div');
  iframe.style.minHeight = '300px';
  iframe.style.width = '100%';
  iframe.style.backgroundColor = '#fff';
  iframe.contentEditable = true;
  iframe.className = 'gallery';
  var value = t.textarea.value;
  t.doc = iframe;
  if (value != '') {
    t.doc.innerHTML = value;
  }
  editor.appendChild(iframe);
  t.observe();
};

YSeditorPDF.prototype = {
  observe() {
    var t = this;
    t.doc.addEventListener('keydown', function (e) {
      e.preventDefault();
    });

    t.doc.addEventListener('click', (e) => {
      if (e.target.tagName == 'IMG') {
        e.target.remove();
        setTimeout(function () {
          t.textarea.innerHTML = t.doc.innerHTML;
        }, 10);
      }
    });

    t.doc.addEventListener('drop', (e) => {
      e.preventDefault();
      var originalData = e.dataTransfer.getData('text/html');
      if (originalData && originalData != '' && originalData.match(/icon_pdf/i)) {
        t.doc.innerHTML += originalData;
        t.textarea.innerHTML = t.doc.innerHTML;
      }
    });
  }
};

function YSeditorObject(editor) {
  var t = this;
  window.settingsLogo = false;
  window.imageSettings = false;

  t.container = editor;
  t.seeHTML = false;
  var textarea = editor.querySelector('textarea');
  if (textarea) {
    textarea.style.display = 'none';
    textarea.style.fontFamily = 'monotype';
    t.textarea = textarea;
  }
  t.basicColors();
  var iframe = document.createElement('div');
  iframe.style.minHeight = '300px';
  iframe.style.width = '100%';
  iframe.style.overflow = 'hidden';
  iframe.style.fontFamily = 'sans-serif';
  iframe.contentEditable = true;
  iframe.innerHTML = t.textarea.value;
  document.execCommand('defaultParagraphSeparator', false, 'p');
  t.toolBar();
  t.doc = iframe;
  editor.appendChild(iframe);
  var imgs = iframe.getElementsByTagName('img');
  if (imgs) {
    for (var i = 0; i < imgs.length; i++) {
      t.imageAction(imgs[i]);
    }
  }
  t.observe();
  window.addEventListener('scroll', function () {
    if (imageSettings && settingsLogo) {
      var rect = imageSettings.getBoundingClientRect();
      settingsLogo.innerHTML = '\u2699';
      settingsLogo.style.fontSize = '40px';
      settingsLogo.style.fontWeight = '800';
      settingsLogo.style.pointerEvents = 'none';
      settingsLogo.style.position = 'fixed';
      settingsLogo.style.top = ((rect.top + ((rect.bottom - rect.top) / 2)) - 20) + 'px';
      settingsLogo.style.left = ((rect.left + ((rect.right - rect.left) / 2)) - 20) + 'px';
    }
  });
};

YSeditorObject.prototype = {
  basicColors: function () {
    this.colors = ['#232323', '#f3f3f3', '#2393b1', '#dfad2f', '#f44336', '#8bc34a']
  },

  saveSelection: function () {
    if (window.getSelection) {
      var sel = window.getSelection();
      if (sel.getRangeAt && sel.rangeCount) {
        return sel.getRangeAt(0);
      }
    } else if (document.selection && document.selection.createRange) {
      return document.selection.createRange();
    }
    return null;
  },

  restoreSelection: function () {
    var range = this.textAreaSelection;
    if (range) {
      if (window.getSelection) {
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
      } else if (document.selection && range.select) {
        range.select();
      }
    }
  },
  observe: function () {
    var t = this;
    t.doc.addEventListener('keyup', function () {
      t.saveToTextarea();
    });

    t.doc.addEventListener('drop', (e) => {
      if (document.caretRangeFromPoint) { // Chrome
        t.textAreaSelection = document.caretRangeFromPoint(e.clientX, e.clientY);
      } else if (e.rangeParent) { // Firefox
        t.textAreaSelection = document.createRange();
        range.setStart(e.rangeParent, e.rangeOffset);
      }
      var text = e.dataTransfer.getData('text');
      var srcFrame = document.querySelector('[name="aide"]');
      var alt = '';
      if (srcFrame) {
        var img = srcFrame.contentDocument.querySelector('[src="' + text.replace(window.location.protocol + '//' + window.location.host + '/', '../') + '"]');
        alt = img.alt;
      }
      var data = e.dataTransfer.getData('URL');
      if (data && data != '') {
        if (data.match(/media([0-9]+)_s./i)) {
          e.preventDefault();
          var rwr = data.replace('_s.', '_p.');
          this.image(rwr, alt);
        } else if (data.match(/([0-9]+).pdf/i)) {
          var id = data.match(/([0-9]+).pdf/i);
        }
      }
    });

  },

  toolBar: function () {
    var toolBar = document.createElement('nav');
    toolBar.id = "toolBarEditor";
    this.container.insertBefore(toolBar, this.textarea);
    this.options(toolBar);
  },

  options: function (node) {
    var t = this;
    var opts = ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull', 'bold', 'italic', 'underline', 'strikethrough', 'insertUnorderedList', 'textList', 'paint', 'link', 'seeMore', 'code'];

    for (var i = 0; i < opts.length; i++) {
      if (opts[i] == 'textList') {
        var choices = [
          ['format du texte', ''],
          ['paragraphe', 'p'],
          ['Titre 2', 'h2'],
          ['Titre 3', 'h3'],
          ['Titre 4', 'h4'],
          ['citation', 'blockquote']
        ];
        var select = document.createElement('select');
        for (var nb = 0; nb < choices.length; nb++) {
          var choice = document.createElement('option');
          choice.value = choices[nb][1];
          choice.append(document.createTextNode(choices[nb][0]));
          select.appendChild(choice);
        }
        select.addEventListener('mousedown', function (e) {
          t.textAreaSelection = t.saveSelection();
        });
        select.addEventListener('change', function (e) {
          if (this.value != '') {
            t.execCmdWithVal('formatBlock', this.value);
          }
          this.value = '';
        });
        select.value = '';
        node.appendChild(select);
      } else {
        var span = document.createElement('span');
        span.className = 'icon-' + opts[i] + ' btnEditor';
        span.setAttribute('data-action', opts[i]);
        span.addEventListener('mousedown', function (e) {
          e.preventDefault();
          t.textAreaSelection = t.saveSelection();
          var attr = this.getAttribute('data-action');
          if (this.seeHTML && attr != 'code') {
            return;
          }
          switch (attr) {
            case 'paint':
              t.chooseColor();
              break;
            case 'link':
              t.createLink();
              break;
            case 'code':
              t.switchToHtml();
              break;
            case 'seeMore':
              t.insertSeeMore();
              break;
            default:
              t.execCmd(attr);
          }
        });
        node.appendChild(span);
        span = null;
      }
    }
  },

  execCmd: function (cmd) {
    this.restoreSelection();
    document.execCommand(cmd, false, null);
    this.saveToTextarea();
  },

  execCmdWithVal: function (cmd, val) {
    this.restoreSelection();
    if (cmd == 'formatBlock') {
      if (
        this.textAreaSelection.commonAncestorContainer.nodeName == 'UL' ||
        this.textAreaSelection.commonAncestorContainer.parentNode.nodeName == 'LI' ||
        this.textAreaSelection.commonAncestorContainer.parentNode.nodeName == 'UL'
      ) {
        document.execCommand('insertUnorderedList', false, null);
      }
    }
    document.execCommand(cmd, false, val);
    this.saveToTextarea();
  },

  saveToTextarea: function () {
    var value = this.doc.innerHTML;
    value = value.split('<b>').join('<strong>');
    value = value.split('</b>').join('</strong>');
    value = value.split('<i>').join('<em>');
    value = value.split('</i>').join('</em>');
    value = value.split('><').join('>\n<');
    this.textarea.value = value;
  },

  insertSeeMore: function () {
    this.restoreSelection();
    var hr = '<hr class="resumeBis">';
    this.execCmdWithVal('insertHTML', hr);
    if (this.doc.innerHTML.match('<hr class="resume">') != null) {
      this.doc.innerHTML = this.doc.innerHTML.replace('<hr class="resume">', '');
    }
    this.doc.innerHTML = this.doc.innerHTML.replace('<hr class="resumeBis">', '<hr class="resume">');
    this.saveToTextarea();
  },

  switchToHtml: function () {
    var d = this.doc;
    var t = this.textarea;
    var c = this.container;
    if (this.seeHTML) {
      t.style.display = 'none';
      d.innerHTML = t.value;
      d.style.display = 'block';
      c.className = c.className.replace(' codeView', '');
      this.seeHTML = false;
      var imgs = d.getElementsByTagName('img');
      if (imgs) {
        for (var i = 0; i < imgs.length; i++) {
          this.imageAction(imgs[i]);
        }
      }
    } else {
      var style = window.getComputedStyle(d, null);
      t.style.width = style.width;
      t.style.height = style.height;
      t.style.display = 'block';
      d.style.display = 'none';
      c.className += ' codeView';
      this.seeHTML = true;
    }
  },

  createLink: function () {
    var t = this;
    var overlay = document.createElement('div');
    overlay.className = 'overlay';
    overlay.addEventListener('click', function (e) {
      if (e.target == this) {
        this.remove();
      }
    });
    var titl = document.createElement('p');
    var titlVal = document.createTextNode('Selectionnez une couleur :');
    var linkCreator = document.createElement('div');
    linkCreator.className = 'editorPopup';
    var form = document.createElement('div');
    form.className = 'linkOptionsEditor';
    /* TEXTE DU LIEN */
    var text = document.createElement('input');
    text.type = 'text';
    text.value = t.textAreaSelection.toString();
    text.placeholder = 'texte du lien';
    /* URL DU LIEN */
    var url = document.createElement('input');
    url.type = 'text';
    url.placeholder = 'URL du lien';
    /* TYPE DE LIEN */
    var type = document.createElement('select');
    var opts = [
      ['url', ''],
      ['téléphone', 'tel:'],
      ['mail', 'mailto:']
    ];
    for (var nb = 0; nb < opts.length; nb++) {
      var option = document.createElement('option');
      option.value = opts[nb][1];
      option.innerHTML = opts[nb][0];
      type.appendChild(option);
    }
    /* TARGET BLANK */
    var blank = document.createElement('input');
    blank.type = 'checkbox';
    blank.id = 'targetBlankEditor';
    var blankLabel = document.createElement('label');
    blankLabel.innerHTML = 'Ouvrir sur une nouvelle page.';
    blankLabel.htmlFor = 'targetBlankEditor';
    /* VALIDER */
    var button = document.createElement('span');
    button.className = 'validLink';
    button.innerHTML = 'Valider';
    button.addEventListener('click', function () {
      var textLien = text.value;
      var urlLien = url.value;
      var typeLien = type.value;
      var isBlank = (blank.checked) ? 'target="_blank"' : '';
      var a = '<a rel="noreferrer" href="' + typeLien + urlLien + '" ' + isBlank + '>' + String(textLien) + '</a>';
      t.execCmdWithVal('insertHTML', a);
      this.parentElement.parentElement.remove();
    });

    linkCreator.appendChild(text);
    linkCreator.appendChild(url);
    linkCreator.appendChild(type);
    linkCreator.appendChild(blank);
    linkCreator.appendChild(blankLabel);
    linkCreator.appendChild(button);

    overlay.appendChild(linkCreator);
    document.body.appendChild(overlay);
  },
  image: function (src, alt) {
    var t = this;

    if (src && src != null) {
      var num = src.match(/media([0-9]+)_p./i)[1];
      var id = 'img' + num;
      var img = "<img src='" + src + "' alt='" + alt + "' id='" + id + "' style='display:block; float:none; margin:auto'>";

      t.execCmdWithVal("insertHTML", img);
      img = document.querySelectorAll('[src="' + src + '"]');
      if (img) {
        for (var i = 0; i < img.length; i++) {
          t.imageAction(img[i]);
        }
      }
    }
  },
  imageAction: function (img) {
    var t = this;
    img.onmouseenter = function (e) {
      this.style.cursor = 'pointer';
      this.style.transition = 'all .3s';
      this.style.opacity = '.8';
      var rect = this.getBoundingClientRect();
      window.imageSettings = this;
      window.settingsLogo = document.createElement('span');
      settingsLogo.innerHTML = '\u2699';
      settingsLogo.style.fontSize = '40px';
      settingsLogo.style.fontWeight = '800';
      settingsLogo.style.pointerEvents = 'none';
      settingsLogo.style.position = 'fixed';
      settingsLogo.style.top = ((rect.top + ((rect.bottom - rect.top) / 2)) - 20) + 'px';
      settingsLogo.style.left = ((rect.left + ((rect.right - rect.left) / 2)) - 20) + 'px';
      document.body.appendChild(settingsLogo);
    };


    img.onmouseout = function () {
      this.style.cursor = 'default';
      this.style.opacity = '1';
      if (window.settingsLogo) {
        settingsLogo.remove();
        window.settingsLogo = false;
        window.imageSettings = false;
      }
    };

    img.onmousedown = function () {
      this.style.cursor = 'default';
      this.style.opacity = '1';
      if (window.settingsLogo) {
        settingsLogo.remove();
        window.settingsLogo = false;
        window.imageSettings = false;
      }
    };
    img.onclick = clickFunc;
    img.addEventListener('dragend', function (e) {
      this.onclick = clickFunc;
    });

    function clickFunc(e) {
      if (window.settingsLogo) {
        settingsLogo.remove();
        window.settingsLogo = false;
        window.imageSettings = false;
      }
      var overlay = document.createElement('div');
      overlay.className = 'overlay';
      overlay.addEventListener('click', function (e) {
        if (e.target == this) {
          this.remove();
        }
      });
      var titl = document.createElement('p');
      var titlVal = document.createTextNode('Options de l\'image :');
      var opts = document.createElement('div');
      opts.className = 'editorPopup';

      titl.appendChild(titlVal);
      opts.appendChild(titl);

      titl = document.createElement('p');
      titlVal = document.createTextNode('Largeur :');
      titl.appendChild(titlVal);
      opts.appendChild(titl);

      var width = document.createElement('div');
      var wInput = document.createElement('input');
      wInput.type = 'text';
      wInput.placeholder = 'width';
      wInput.value = img.style.width.replace('%', '');
      var wLabel = document.createElement('label');
      wLabel.innerHTML = '%';
      width.className = 'widthInput';
      width.appendChild(wInput);
      width.appendChild(wLabel);
      opts.appendChild(width);

      titl = document.createElement('p');
      titlVal = document.createTextNode('Marges :');
      titl.appendChild(titlVal);
      opts.appendChild(titl);

      /* MarginLeft */
      var ml = document.createElement('div');
      var mli = document.createElement('input');
      mli.type = 'text';
      mli.placeholder = '0';
      mli.value = img.style.marginLeft.replace('px', '');
      var mll = document.createElement('label');
      mll.innerHTML = 'left';
      ml.className = 'marginInput';
      ml.appendChild(mll);
      ml.appendChild(mli);
      opts.appendChild(ml);

      /* MarginTop */
      var mt = document.createElement('div');
      var mti = document.createElement('input');
      mti.type = 'text';
      mti.placeholder = '0';
      mti.value = img.style.marginTop.replace('px', '');
      var mtl = document.createElement('label');
      mtl.innerHTML = 'top';
      mt.className = 'marginInput';
      mt.appendChild(mtl);
      mt.appendChild(mti);
      opts.appendChild(mt);

      /* MarginRight */
      var mr = document.createElement('div');
      var mri = document.createElement('input');
      mri.type = 'text';
      mri.placeholder = '0';
      mri.value = img.style.marginRight.replace('px', '');
      var mrl = document.createElement('label');
      mrl.innerHTML = 'right';
      mr.className = 'marginInput';
      mr.appendChild(mrl);
      mr.appendChild(mri);
      opts.appendChild(mr);

      /* MarginBottom */
      var mb = document.createElement('div');
      var mbi = document.createElement('input');
      mbi.type = 'text';
      mbi.placeholder = '0';
      mbi.value = img.style.marginBottom.replace('px', '');
      var mbl = document.createElement('label');
      mbl.innerHTML = 'bottom';
      mb.className = 'marginInput';
      mb.appendChild(mbl);
      mb.appendChild(mbi);
      opts.appendChild(mb);

      /* Allignement */
      titl = document.createElement('p');
      titlVal = document.createTextNode('Alignement :');
      titl.appendChild(titlVal);
      opts.appendChild(titl);

      var align = document.createElement('div');
      align.className = 'alignImage';
      var al = document.createElement('input');
      al.type = 'radio';
      al.checked = (img.style.float == 'left') ? true : false;
      al.name = 'align';
      al.id = 'alignLeft';
      var all = document.createElement('label');
      all.htmlFor = 'alignLeft';
      all.innerHTML = 'Gauche';

      var ar = document.createElement('input');
      ar.type = 'radio';
      ar.checked = (img.style.float == 'right') ? true : false;
      ar.name = 'align';
      ar.id = 'alignRight';
      var arl = document.createElement('label');
      arl.htmlFor = 'alignRight';
      arl.innerHTML = 'Droite';

      var ac = document.createElement('input');
      ac.type = 'radio';
      ac.checked = (img.style.float == 'none') ? true : false;
      ac.name = 'align';
      ac.id = 'alignCenter';
      var acl = document.createElement('label');
      acl.htmlFor = 'alignCenter';
      acl.innerHTML = 'Centre';

      align.appendChild(al);
      align.appendChild(all);
      align.appendChild(ac);
      align.appendChild(acl);
      align.appendChild(ar);
      align.appendChild(arl);
      opts.appendChild(align);

      var validBtn = document.createElement('button');
      validBtn.className = 'validImage';
      validBtn.innerHTML = 'valider';
      validBtn.addEventListener('click', function () {
        var w = wInput.value;
        var mtv = mti.value;
        var mbv = mbi.value;
        var mlv = mli.value;
        var mrv = mri.value;
        img.style.width = w + '%';
        img.style.marginTop = mtv + 'px';
        img.style.marginBottom = mbv + 'px';
        img.style.marginLeft = mlv + 'px';
        img.style.marginRight = mrv + 'px';
        if (al.checked || ar.checked) {
          img.style.float = (al.checked) ? 'left' : 'right';
        } else {
          if (ac.checked) {
            img.style.marginLeft = 'auto';
            img.style.marginRight = 'auto';
            img.style.float = 'none';
          }
        }
        overlay.remove();
        t.saveToTextarea();
      });
      var removeBtn = document.createElement('button');
      removeBtn.innerHTML = 'supprimer';
      removeBtn.className = 'removeImage';
      removeBtn.addEventListener('click', function () {
        overlay.remove();
        img.remove();
        t.saveToTextarea();
      });
      var valid = document.createElement('div');
      valid.className = 'validBar';
      valid.appendChild(validBtn);
      valid.appendChild(removeBtn);
      opts.appendChild(valid);

      overlay.appendChild(opts);
      document.body.appendChild(overlay);
    };
  },
  chooseColor: function () {
    var t = this;
    var overlay = document.createElement('div');
    overlay.className = 'overlay';
    overlay.addEventListener('click', function (e) {
      if (e.target == this) {
        this.remove();
      }
    });
    var titl = document.createElement('p');
    var titlVal = document.createTextNode('Selectionnez une couleur :');
    var colors = document.createElement('div');
    colors.className = 'editorPopup';

    titl.appendChild(titlVal);
    colors.appendChild(titl);

    for (var i = 0; i < t.colors.length; i++) {
      var c = t.colors[i];
      var selector = document.createElement('span');
      selector.setAttribute('data-color', c);
      selector.style.backgroundColor = c;
      selector.className = 'colorPickerElement';
      selector.addEventListener('mousedown', function (e) {
        e.preventDefault();
        t.saveSelection();
        t.execCmdWithVal('foreColor', this.getAttribute('data-color'));
        overlay.remove();
      });
      colors.appendChild(selector);
    }
    overlay.appendChild(colors);

    var customizer = document.createElement('div');
    customizer.className = 'customColor';
    var customizerInput = document.createElement('input');
    customizerInput.type = 'text';
    customizerInput.value = '000000';

    customizerInput.addEventListener('keyup', function () {
      this.value.replace(/#/gi, '');
      if (this.value.length >= 3 && this.value.length <= 6) {
        this.nextElementSibling.style.backgroundColor = '#' + this.value;
        this.nextElementSibling.setAttribute('data-color', '#' + this.value);
      }
    });
    var customizerPreview = document.createElement('span');
    customizerPreview.className = 'colorPickerElement';
    customizerPreview.setAttribute('data-color', '#000000');
    customizerPreview.style.backgroundColor = '#000000';

    customizerPreview.addEventListener('click', function (e) {
      t.execCmdWithVal('foreColor', this.getAttribute('data-color'));
      overlay.remove();
    });
    customizer.appendChild(customizerInput);
    customizer.appendChild(customizerPreview);
    colors.appendChild(customizer);
    document.body.appendChild(overlay);
  },

  addColor: function (colors) {
    this.colors = this.colors.concat(colors);
  }
};

/*------------------------------------------------------------------------------------------------------------*/
function adminCentre() {
  var zg = document.getElementById('gauche');
  var zc = document.getElementById('centre');
  var cc = document.getElementById('contenu_centre');
  var bc = document.getElementById('btn_centre');
  var zd = document.getElementById('droite');

  if (localStorage.getItem('centre') && localStorage.getItem('centre') == 'open') {
    zg.className = zg.className.replace('shadow_gauche', '');
    if (cc) {
      cc.style.display = 'block';
    }
    bc.style.left = 'calc(40% - 40px)';
    bc.style.transform = 'rotate(180deg)';
    bc.className = bc.className.replace('cache', 'ouvert');
    zc.style.left = '15%';
    zc.style.width = '25%';
    zc.style.paddingRight = '0';
    zd.style.left = '40%';
    zd.style.width = '60%';
  }
  setTimeout(function () {
    zd.style.transition = 'all .3s';
    zg.style.transition = 'all .3s';
    zc.style.transition = 'all .3s';
    bc.style.transition = 'all .3s';
  }, 400);

  bc.addEventListener('click', function (e) {
    e.preventDefault();
    this.style.backgroundImage = 'url(../../icones/volet.png)';
    this.style.cursor = 'default';
    if (this.className.match('cache') != null) {
      zg.className = zg.className.replace(' shadow_gauche', '').replace('shadow_gauche', '');
      zc.style.left = '15%';
      zc.style.width = '25%';
      zc.style.paddingRight = '0';
      zd.style.left = '40%';
      zd.style.width = '60%';
      if (cc) {
        cc.style.display = 'block';
      }
      bc.style.left = 'calc(40% - 40px)';
      this.style.transform = 'rotate(180deg)';
      this.className = this.className.replace('cache', 'ouvert');
      localStorage.setItem('centre', 'open');
    } else {
      zg.className += ' shadow_gauche';
      zc.style.left = '0';
      zc.style.width = '15%';
      zc.stylepaddingRight = '0';
      zd.style.left = '15%';
      zd.style.width = '85%';

      bc.style.left = 'calc(15% - 40px)';
      this.style.transform = 'rotate(0deg)';
      this.className = this.className.replace('ouvert', 'cache');
      if (cc) {
        cc.style.display = 'none';
      }
      localStorage.setItem('centre', 'close');
    }
  });
};


/*------------------------------------------------------------------------------------------------------------*/
function update_form_page(nb) {
  var liensGabarit = document.querySelectorAll('#liens_gabarit a');
  if (liensGabarit) {
    for (var i = 0; i < liensGabarit.length; i++) {
      if (i + 1 == nb) {
        liensGabarit[i].className = 'active';
      } else {
        liensGabarit[i].className = '';
      }
    }
  }
  update_zones(nb);
};

function form_page() {
  var nb = '';

  if (localStorage.getItem('gabaritId') && !document.getElementById('ligne_coloree')) {
    nb = localStorage.getItem('gabaritId');
  } else {
    var gabaritID = document.getElementById('gabaritId');
    if (gabaritID) {
      nb = gabaritID.value;
      localStorage.getItem('gabaritId', nb);
    }
  }

  var menuGabarit = document.querySelector('#liens_gabarit ul');

  if (menuGabarit) {
    for (var i = 0; i < 5; i++) {
      var li = document.createElement('li');
      var lien = document.createElement('a');
      lien.href = '#';
      lien.setAttribute('data-id', (i + 1));
      if (nb == i + 1) {
        lien.className = 'active';
      }
      lien.addEventListener('click', function (e) {
        e.preventDefault();
        var value = this.getAttribute('data-id');
        update_form_page(parseInt(value));
        localStorage.setItem('gabaritId', value);
        document.getElementById('gabaritId').value = value;
      });
      li.appendChild(lien);
      menuGabarit.appendChild(li);
    }
  }
  update_zones(parseInt(nb));

};

function update_zones(nb) {
  var zone1 = document.getElementById('zone1');
  var zone2 = document.getElementById('zone2');
  var zone3 = document.getElementById('zone3');
  if (zone1 && zone2 && zone3) {
    switch (nb) {
      case 1:
        zone1.style.display = 'block';
        zone1.style.width = '100%';

        zone2.style.display = 'none';
        zone2.style.width = '100%';

        zone3.style.display = 'none';
        zone3.style.width = '100%';
        break;
      case 2:
        zone1.style.display = 'block';
        zone1.style.width = '100%';

        zone2.style.display = 'block';
        zone2.style.width = '50%';

        zone3.style.display = 'block';
        zone3.style.width = '50%';
        break;
      case 3:
        zone1.style.display = 'block';
        zone1.style.width = '50%';

        zone2.style.display = 'block';
        zone2.style.width = '50%';

        zone3.style.display = 'block';
        zone3.style.width = '100%';
        break;
      case 4:
        zone1.style.display = 'block';
        zone1.style.width = '100%';

        zone2.style.display = 'block';
        zone2.style.width = '100%';

        zone3.style.display = 'block';
        zone3.style.width = '100%';
        break;
      case 5:
        zone1.style.display = 'block';
        zone1.style.width = '33.3%';

        zone2.style.display = 'block';
        zone2.style.width = '33.3%';

        zone3.style.display = 'block';
        zone3.style.width = '33.3%';
        break;
    }
  }
};


document.addEventListener('DOMContentLoaded', function () {
  dossierDropdown();
  var editors = new YSeditor('.YSeditor');
  adminCentre();
});