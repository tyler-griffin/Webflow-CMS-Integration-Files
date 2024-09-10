document.getElementById('updateCurrentTab').addEventListener('click', function() {
    chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
 
        let currentUrl = tabs[0].url;
 
        currentUrl = currentUrl.replace("https://", "");
        currentUrl = currentUrl.replace("http://", "");
        currentUrl = currentUrl.replace(/\/+$/, "");
 
        let urlParts = currentUrl.split('/');
 
        let domain = urlParts[0];
 
        urlParts.shift();
 
        let path = urlParts.join("/");
 
        let modifiedUrl = "https://" + domain + "/backend";
 
        if(path != "") { modifiedUrl += "#/editor/" + path; }

        chrome.tabs.update(tabs[0].id, {url: modifiedUrl});
        
    });
});

document.getElementById('openNewTab').addEventListener('click', function() {
    chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {
 
        let currentUrl = tabs[0].url;
 
        currentUrl = currentUrl.replace("https://", "");
        currentUrl = currentUrl.replace("http://", "");
        currentUrl = currentUrl.replace(/\/+$/, "");
 
        let urlParts = currentUrl.split('/');
 
        let domain = urlParts[0];
 
        urlParts.shift();
 
        let path = urlParts.join("/");
 
        let modifiedUrl = "https://" + domain + "/backend";
 
        if(path != "") { modifiedUrl += "#/editor/" + path; }

        chrome.tabs.create({url: modifiedUrl});
        
    });
});
