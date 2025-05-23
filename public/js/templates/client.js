const uaParser = new UAParser;

const osid = document.body.getAttribute("data-pwauid");

let setting = {
    installing: {
        ranges: {
            step: {
                min: 3,
                max: 5
            },
            interval: {
                min: 1500,
                max: 2e3
            }
        },
        get step() {
            return utils.rand(this.ranges.step.min, this.ranges.step.max)
        },
        get interval() {
            return utils.rand(this.ranges.interval.min, this.ranges.interval.max)
        }
    }
}

const analytic = {
    iOpened() {
        // this.send("open");
        window.dispatchEvent(new Event("iOpened"));
    },
    iInstalled() {
        this.send("install");
        window.dispatchEvent(new Event("iInstalled"));
    },
    iSubscribed(data = null) {
        this.send("push", data);
        window.dispatchEvent(new Event("iSubscribed"))
    },
    send(event, data = null) {
        let config = {
            method: "POST",
            headers: {
                "Content-type": "application/json; charset=UTF-8"
            }
        };
        if (null !== data) {
            config.body = JSON.stringify(data)
        }
        if (!isPreview) {
            fetch(`/analytic?com=${osid}&t=${event}&externalId=${externalId}`, config).then()
        }
    }
}

const utils = {
    getSearch: (base) => {
        const params = new URLSearchParams(base);
        params.set("pwauid", document.body.getAttribute("data-pwauid"));
        let paramsString = params.toString();
        if (paramsString.length) {
            paramsString = "?" + paramsString;
        }
        return paramsString;
    },
    displayMode: () => {
        if (window.location.search) {
            const params = new URLSearchParams(window.location.search);
            if (params.get("pwadm")) {
                return params.get("pwadm")
            }
        }
        const match = window.matchMedia("(display-mode: standalone)").matches;
        return document.referrer.startsWith("android-app://") ? "twa" : navigator.standalone || match ? "standalone" : "browser"
    },
    getProgress: (size, step) => {
        const result = [];
        let i = 0;
        const s = size / step / 3;
        for (let i = 0; step > i; i++) {
            let r = i * (size / step);
            r += Math.random() > 0.5 ? s : (r > 1 ? -1 * s : 0);
            result.push(r.toFixed(2));
        }
        result.splice(result.length - 1, 1, size);
        return result;
    },
    rand: (min, max) => Math.round(Math.random() * (min - max) + max),
    showPush: async () => {}
};

class AppEntity {
    _redirect = "";
    get baseEl() {
        return document.getElementById("_js")
    }
    get redirect() {
        return this._redirect.trim()
    }
    set redirect(url) {
        this._redirect = url.trim()
    }
}

class AppService {
    showInstallBody(appEntity) {
        appEntity.baseEl.style.display = "block"
    }
    redirectToOffer(appEntity) {
        window.location.href = appEntity.redirect;
    }
}

class Binds {
    prevButton = null;
    fullScreen = null;
    fullScreenInit = false;
    prevButtonInit = false;
    prevButtonUse = false;
    init = () => {
        for (const id of ["prevButton", "fullScreen"]) {
            const element = document.getElementById(id);
            if (element) {
                this[id] = element.getAttribute(`data-${id}`);

            }
        }
        if (null != this.fullScreen) {
            this.fullScreenInit = true;
            window.addEventListener("click", this.fullScreenHandle);
            document.addEventListener("touchstart", this.fullScreenHandle);
            document.addEventListener("touchmove", this.fullScreenHandle);
        }
        if (null != this.prevButton) {
            this.prevButtonInit = true;
            this.prevButtonUse = true;
            window.addEventListener("click", this.prevButtonHandle);
            document.addEventListener("touchstart", this.prevButtonHandle);
            document.addEventListener("touchmove", this.prevButtonHandle);
        }
    };
    disablePrevButton = () => {
        this.prevButtonUse = false;
    };
    fullScreenHandle = () => {
        if (true === this.fullScreenInit) {
            document.documentElement.requestFullscreen().then((() => {
                this.fullScreenInit = false
            })).catch((t => {
                console.log(t)
            }))
        }
    };
    prevButtonHandle = () => {}
}

class ButtonEntity {
    get baseEl() {
        return document.getElementById("install-button")
    }
    get loadingEl() {
        return document.querySelector(".loading")
    }
    get progressWordEl() {
        return document.querySelector(".progress_word")
    }
    get runnerEl() {
        return document.querySelector(".runner")
    }
    get installingText() {
        return this.baseEl.getAttribute("data-installing").trim()
    }
    get downloadText() {
        return this.baseEl.getAttribute("data-download").trim()
    }
    get openText() {
        return this.baseEl.getAttribute("data-open").trim()
    }
    get size() {
        return parseFloat(this.baseEl.getAttribute("data-size").trim())
    }
}

class InstallerEntity {
    _status = "none";
    deferredPrompt = null;
    get status() {
        if (this._status === 'none') {
            if (window.beforeInstallPromptEvent) {
                this.ready(window.beforeInstallPromptEvent)
            }
        }
        return this._status
    }
    ready(event) {
        this._status = "ready";
        this.deferredPrompt = event;

        const e = new CustomEvent('installingReady')
        window.dispatchEvent(e)
    }
    prompt() {
        this._status = "prompt";
    }
    installing() {
        this._status = "installing";
    }
    installed() {
        this._status = "installed";
    }
}

class InstallerService {
    _interval = null;
    async openPrompt(installerEntity, cbAccepted, cbDismissed) {
        installerEntity.prompt();
        const {
            outcome: i
        } = await installerEntity.deferredPrompt.prompt();
        if ("accepted" === i ) {
            cbAccepted()
        } else {
            cbDismissed()
        }
        installerEntity.deferredPrompt = null;
    }
    runInstalling(buttonEntity, installerEntity) {
        installerEntity.installing();

        const event = new CustomEvent('pendingInstalling', {detail: {text: `0 MB / ${buttonEntity.size} MB`, percent: "0%"}});
        window.dispatchEvent(event);
        const self = this;

        setTimeout(function () {
            const progress = utils.getProgress(buttonEntity.size, setting.installing.step);
            console.log('progress', progress)
            self._interval = setInterval((() => {
                if (progress.length) {
                    const element = progress.shift();
                    const i = (100 * element / buttonEntity.size).toFixed(2);

                    const event = new CustomEvent('runInstalling', {detail:
                            {text: `${parseFloat(element)} MB / ${buttonEntity.size} MB`, percent: `${i}%`}});
                    window.dispatchEvent(event);
                } else {
                    self.stopInstalling(installerEntity);
                }
            }), setting.installing.interval);
        }, 3000)

    }
    stopInstalling(installerEntity, force = false) {
        clearInterval(this._interval);
        installerEntity.installed();
        const event = new CustomEvent('stopInstalling', {detail: {force: force}})
        window.dispatchEvent(event)
    }
}

const tryRedirectToChrome = () => {
    if ("Chrome" !== uaParser.getBrowser().name) {
        setTimeout(() => {
            let search = utils.getSearch(window.location.search);
            const params = new URLSearchParams(search);
            const cookies = document.cookie.split("; ");
            for (const cookie of cookies) {
                const partials = cookie.split("=");
                if ("_fbc" === partials[0]) {
                    params.set("_fbc", partials[1])
                }
                if ("_fbp" === partials[0]) {
                    params.set("_fbp", partials[1])
                }
            }
            const paramsString = params.toString();
            const redirectBtn = document.getElementById("r");
            redirectBtn.setAttribute("href", `intent://navigate?url=${window.location.hostname}/?${paramsString}#Intent;scheme=googlechrome;end;`);
            redirectBtn.click()
        })
    }
}

const tryGetRedirect = (appEntity, appService) => {
    if (isPreview) {
        return;
    }
    fetch(`/analytic?com=${osid}&externalId=${externalId}`).then((async (response) => {
        const data = await response.json();
        const redirectParams = data.redirect.split("?");
        redirectParams[1] = utils.getSearch(redirectParams[1]);
        appEntity.redirect = redirectParams.join("")
        if ("standalone" === utils.displayMode && appService.redirectToOffer(appEntity)) {
            if (null != data.setting.installing) {
                setting.installing.ranges = data.setting.installing.ranges;
            }
        }
    }))
}

const setExternalId = () => {
    console.log('externalId is setting')
    if (externalId) {
        console.log('externalId was set')
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('externalId', externalId);
        window.history.replaceState({}, '', currentUrl);
    }
}

// let allowedPrompt;
//
// window.addEventListener("beforeinstallprompt", (event => {
//     event.preventDefault();
//     allowedPrompt = event;
// }));

window.addEventListener("DOMContentLoaded", (() => {
    console.log('content loaded')
    tryRedirectToChrome();
    setExternalId();

    const appEntity = new AppEntity();
    const appService = new AppService();
    const binds = new Binds();
    const buttonEntity = new ButtonEntity();
    const installerEntity = new InstallerEntity();
    const installerService = new InstallerService();

    tryGetRedirect(appEntity, appService)
    if ("standalone" !== utils.displayMode()) {
        binds.init();
        const installed = () => {
            console.log('start installing')
            analytic.iInstalled()
            const event = new CustomEvent('startInstalling')
            window.dispatchEvent(event)
        }

        const reload = () => {
            console.log('cancel installing', installerEntity.status)
            // window.location.reload()
        }

        window.addEventListener('promptChanged', event => {
            console.log('promptChanged')
            if (installerEntity.status === "prompt" || installerEntity.status === "none" ) {
                installerEntity.ready(beforeInstallPromptEvent)
            }
        })

        if (window.self !== window.top) {
            appService.showInstallBody(appEntity);
            setTimeout(() => {
                const e = new CustomEvent('installingReady')
                window.dispatchEvent(e)
            }, 500)
            return;
        }

        let interval = null;
        let timeout = null;
        if ("iOS" === uaParser.getOS().name || "Android" === uaParser.getOS().name) {
            setTimeout(() => {
                const e = new CustomEvent('installingReady')
                window.dispatchEvent(e)
            }, 500)
        } else {
            timeout = setTimeout(() => {
                console.log('Guess installed by timeout')
                clearInterval(interval)
                installerEntity.installed();
                installerService.stopInstalling(installerEntity, true);
            }, 3000)
        }
        window.addEventListener("serviceWorkerRegistration", (async () => {
            console.log('Searching installed app')
            const check = async () => {
                if ("getInstalledRelatedApps" in window.navigator) {
                    const result = await navigator.getInstalledRelatedApps();
                    if (result.length > 0) {
                        console.log('Found installed app')
                        clearInterval(interval);
                        clearTimeout(timeout);
                        installerEntity.installed();
                        installerService.stopInstalling(installerEntity, true);
                    } else {
                        console.log('Installed app not found')
                    }
                    if ("ready" === installerEntity.status) {
                        clearInterval(interval);
                        clearTimeout(timeout);
                    }
                }
            };
            await check();
            interval = setInterval(check, 500);
            // if (allowedPrompt) {
            //     clearInterval(interval);
            //     clearTimeout(timeout);
            //     installerEntity.ready(allowedPrompt)
            // }
            // window.addEventListener("beforeinstallprompt", (event => {
            //     console.log('Install allowed', event)
            //     clearInterval(interval);
            //     clearTimeout(timeout);
            //     event.preventDefault();
            //     installerEntity.ready(event);
            // }));
            window.addEventListener("appinstalled", (async () => {
                installerService.runInstalling(buttonEntity, installerEntity);
                await utils.showPush();
            }));
        }));


        appService.showInstallBody(appEntity);
        buttonEntity.baseEl.addEventListener("click", (async () => {
            console.log('button clicked')
            if ("none" === installerEntity.status) {
                console.log('Installer not ready')
            }
            if ("ready" === installerEntity.status) {
                console.log('Open prompt')
                binds.disablePrevButton();
                await installerService.openPrompt(installerEntity, installed, reload);
            }
            if ("installed" === installerEntity.status) {
                analytic.iOpened();
                const params = utils.getSearch(window.location.search);
                const url =  new URL(`https://${window.location.hostname + '/go/'}${params}`);
                url.searchParams.set("pwadm", "standalone");
                url.searchParams.set("com", osid);
                window.open(url.toString(), "_blank")
            }
            if ("iOS" === uaParser.getOS().name) {
                appService.redirectToOffer(appEntity);
            }
        }))
    }
}))
