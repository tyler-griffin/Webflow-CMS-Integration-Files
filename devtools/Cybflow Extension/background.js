const snippets = [
  { id: "block", title: "block", text: "block" },
  { id: "amsd", title: "amsd", text: "amsd" },
  { id: "list", title: "list", text: "list" },
  { id: "text", title: "text", text: "text" },
  { id: "textarea", title: "textarea", text: "textarea"},
  { id: "icon", title: "icon", text: "icon" },
  { id: "img", title: "img", text: "img" },
  { id: "bg", title: "bg", text: "bg" },
  { id: "url", title: "url", text: "url" },
  { id: "button", title: "button", text: "button" },
  { id: "buttontext", title: "buttontext", text: "buttontext" },
  { id: "phone", title: "phone", text: "phone" },
  { id: "email", title: "email", text: "email" },
  { id: "date", title: "date", text: "date" },
  { id: "time", title: "time", text: "time" },
  { id: "vimeobg", title: "vimeobg", text: "vimeobg" },
  { id: "youtubebg", title: "youtubebg", text: "youtubebg" },
  { id: "alertbar", title: "alertbar", text: "alertbar" },
  { id: "popup", title: "popup", text: "popup" },
  { id: "common", title: "common", text: "common" },
  { id: "nav", title: "nav", text: "nav" },
  { id: "logo", title: "logo", text: "logo" },
  { id: "footerlogo", title: "footerlogo", text: "footerlogo" },
  { id: "tag", title: "tag", text: "tag" }
];

for (let snippet of snippets) {
  chrome.contextMenus.create({
    id: snippet.id,
    title: snippet.title,
    contexts: ["editable"]
  });
}

chrome.contextMenus.onClicked.addListener((info, tab) => {
  const snippet = snippets.find(s => s.id === info.menuItemId);
  if (snippet) {
    chrome.scripting.executeScript({
      target: { tabId: tab.id },
      func: pasteText,
      args: [snippet.text]
    });
  }
});

function pasteText(text) {

  function closePopUp() {
    var evt = document.createEvent("MouseEvents");
    evt.initMouseEvent("click", true, true, window,
      0, 0, 0, 0, 0, false, false, false, false, 0, null);
    var a = document.querySelector('[data-automation-id="popover-mask"][data-dd-action-name="expression-editor-settings-list-render-popover"]');
    a.dispatchEvent(evt);      
  }

  const activeElement = document.activeElement;

  if (activeElement && (activeElement.tagName === 'TEXTAREA' || activeElement.tagName === 'INPUT')) {
    if(text == 'cybkey') {
      document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').value = text;
      document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').select();
      document.execCommand('copy');
      document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').value = '';
      setTimeout(() => {
        document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').select();
        document.execCommand('paste');
      }, 50);
      setTimeout(() => {
        document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').select();
      }, 75);
    } else {
      document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').value = 'cybdata';
      document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').select();
      document.execCommand('copy');
      document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').value = '';
      setTimeout(() => {
        document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').select();
        document.execCommand('paste');
      }, 50);
      setTimeout(() => {
        document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = text;
        document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').select();
        document.execCommand('copy');
        document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = '';
        setTimeout(() => {
          document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').select();
          document.execCommand('paste');
        }, 50);

        if(text != 'common' && text != 'profile' && text != 'tag' && text != 'popup' && text != 'alertbar' && text != 'footerlogo' && text != 'logo' && text != 'nav' && text != 'buttontext') {
          setTimeout(() => {
            document.querySelector('[data-automation-id="id-attribute-input"]').select();
            setTimeout(() => {
              document.querySelector('[data-automation-id="ExpressionEditor-fieldWrapper-Custom Attributes"] [data-automation-id="ListAddButton"]').click();
              setTimeout(() => {
                document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').value = 'cybkey';
                document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').select();
                document.execCommand('copy');
                document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').value = '';
                setTimeout(() => {
                  document.querySelector('[data-automation-id="Type--Plugin_Text_Name"]').select();
                  document.execCommand('paste');
                  setTimeout(() => {

                    document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').select();

                    if(text == 'text') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Text';
                    }
                    if(text == 'textarea') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Text Area';
                    }
                    if(text == 'bg') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Background Image';
                    }
                    if(text == 'img') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Image';
                    }
                    if(text == 'url') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Url';
                    }
                    if(text == 'icon') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Icon';
                    }
                    if(text == 'button') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Button';
                    }
                    if(text == 'email') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Email';
                    }
                    if(text == 'phone') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Phone';
                    }
                    if(text == 'vimeobg') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Background Video Vimeo ID';
                    }
                    if(text == 'youtubebg') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = 'Background Video Youtube ID';
                    }

                    if(document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value != '') {
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').select();
                      document.execCommand('copy');
                      document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').value = '';
                      setTimeout(() => {
                        document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').select();
                        document.execCommand('paste');
                        setTimeout(() => {
                          document.querySelector('[data-automation-id="Type--Plugin_Text_Value"]').select();
                        }, 50);
                      }, 50);

                    }
                  }, 50);
                }, 50);
              }, 100);
            }, 100);
          }, 100);
        }
        

      }, 100);
    }
  }
}
