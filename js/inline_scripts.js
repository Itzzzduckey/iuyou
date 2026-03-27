try{(function(w,d){!function(j,k,l,m){if(j.zaraz)console.error("zaraz is loaded twice");else{j[l]=j[l]||{};j[l].executed=[];j.zaraz={deferred:[],listeners:[]};j.zaraz._v="5874";j.zaraz._n="cf8b9a59-90fb-44f4-ada4-f13a9a0d95fe";j.zaraz.q=[];j.zaraz._f=function(n){return async function(){var o=Array.prototype.slice.call(arguments);j.zaraz.q.push({m:n,a:o})}};for(const p of["track","set","debug"])j.zaraz[p]=j.zaraz._f(p);j.zaraz.init=()=>{var q=k.getElementsByTagName(m)[0],r=k.createElement(m),s=k.getElementsByTagName("title")[0];s&&(j[l].t=k.getElementsByTagName("title")[0].text);j[l].x=Math.random();j[l].w=j.screen.width;j[l].h=j.screen.height;j[l].j=j.innerHeight;j[l].e=j.innerWidth;j[l].l=j.location.href;j[l].r=k.referrer;j[l].k=j.screen.colorDepth;j[l].n=k.characterSet;j[l].o=(new Date).getTimezoneOffset();if(j.dataLayer)for(const t of Object.entries(Object.entries(dataLayer).reduce((u,v)=>({...u[1],...v[1]}),{})))zaraz.set(t[0],t[1],{scope:"page"});j[l].q=[];for(;j.zaraz.q.length;){const w=j.zaraz.q.shift();j[l].q.push(w)}r.defer=!0;for(const x of[localStorage,sessionStorage])Object.keys(x||{}).filter(z=>z.startsWith("_zaraz_")).forEach(y=>{try{j[l]["z_"+y.slice(7)]=JSON.parse(x.getItem(y))}catch{j[l]["z_"+y.slice(7)]=x.getItem(y)}});r.referrerPolicy="origin";r.src="/cdn-cgi/zaraz/s.js?z="+btoa(encodeURIComponent(JSON.stringify(j[l])));q.parentNode.insertBefore(r,q)};["complete","interactive"].includes(k.readyState)?zaraz.init():j.addEventListener("DOMContentLoaded",zaraz.init)}}(w,d,"zarazData","script");window.zaraz._p=async d$=>new Promise(ea=>{if(d$){d$.e&&d$.e.forEach(eb=>{try{const ec=d.querySelector("script[nonce]"),ed=ec?.nonce||ec?.getAttribute("nonce"),ee=d.createElement("script");ed&&(ee.nonce=ed);ee.innerHTML=eb;ee.onload=()=>{d.head.removeChild(ee)};d.head.appendChild(ee)}catch(ef){console.error(`Error executing script: ${eb}\n`,ef)}});Promise.allSettled((d$.f||[]).map(eg=>fetch(eg[0],eg[1])))}ea()});zaraz._p({"e":["(function(w,d){})(window,document)"]});})(window,document)}catch(e){throw fetch("/cdn-cgi/zaraz/t"),e;};

(function(w,d){})(window,document)

(function(w,d){;w.zarazData.executed.push("Pageview");})(window,document)

(function(w,d){})(window,document)

(function(w,d){{(function(w,d){zaraz.__zarazMCListeners={"google-analytics_v4_cenO":["visibilityChange"]};})(window,document);}})(window,document)

(function(w,d){{(function(w,d){zaraz.__zarazMCListeners={"google-analytics_v4_cenO":["visibilityChange"]};})(window,document);}})(window,document)


        lucide.createIcons();

        const themeBtn = document.getElementById('theme-btn');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            
            if (themeBtn) {
                // Lucide replaces the <i> element with SVG, so we need to recreate the <i> element
                const iconName = theme === 'light' ? 'sun' : 'moon';
                // Remove any existing SVG or <i> element that Lucide created
                themeBtn.innerHTML = '';
                // Recreate the <i> element with the correct icon
                const newIcon = document.createElement('i');
                newIcon.setAttribute('data-lucide', iconName);
                newIcon.id = 'theme-icon';
                themeBtn.appendChild(newIcon);
                // Update the themeIcon reference
                const updatedThemeIcon = document.getElementById('theme-icon');
                if (updatedThemeIcon) {
                    // Recreate the icon
                    lucide.createIcons();
                }
            }
        }

        const saved = localStorage.getItem('theme');
        if (saved) {
            setTheme(saved);
        } else {
            setTheme('dark');
        }

        if (themeBtn) {
            themeBtn.addEventListener('click', () => {
                const current = html.getAttribute('data-theme');
                setTheme(current === 'dark' ? 'light' : 'dark');
            });
        }
    



        let confirmResolve = null;
        let confirmResult = false;
        let confirmPending = false;

        function customConfirm(message, title = 'Are you sure?') {

            if (confirmPending) {
                return false; 
            }

            return new Promise((resolve) => {
                confirmPending = true;
                confirmResolve = resolve;
                const modal = document.getElementById('customConfirmModal');
                const titleEl = document.getElementById('customConfirmTitle');
                const textEl = document.getElementById('customConfirmText');
                const cancelBtn = document.getElementById('customConfirmCancel');
                const okBtn = document.getElementById('customConfirmOk');


                titleEl.textContent = title;
                textEl.textContent = message;


                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                lucide.createIcons();


                const cleanup = () => {
                    modal.classList.remove('show');
                    document.body.style.overflow = '';
                    cancelBtn.onclick = null;
                    okBtn.onclick = null;
                    modal.onclick = null;
                    confirmPending = false;
                };


                cancelBtn.onclick = () => {
                    cleanup();
                    confirmResult = false;
                    if (confirmResolve) {
                        confirmResolve(false);
                        confirmResolve = null;
                    }
                };


                okBtn.onclick = () => {
                    cleanup();
                    confirmResult = true;
                    if (confirmResolve) {
                        confirmResolve(true);
                        confirmResolve = null;
                    }
                };

   
                modal.onclick = (e) => {
                    if (e.target === modal) {
                        cleanup();
                        confirmResult = false;
                        if (confirmResolve) {
                            confirmResolve(false);
                            confirmResolve = null;
                        }
                    }
                };


                const escapeHandler = (e) => {
                    if (e.key === 'Escape') {
                        cleanup();
                        confirmResult = false;
                        if (confirmResolve) {
                            confirmResolve(false);
                            confirmResolve = null;
                        }
                        document.removeEventListener('keydown', escapeHandler);
                    }
                };
                document.addEventListener('keydown', escapeHandler);
            });
        }


        function confirmSubmit(event, message, title) {
            event.preventDefault();
            const form = event.target;
            customConfirm(message, title).then(confirmed => {
                if (confirmed) {
                    form.submit();
                }
            });
            return false;
        }


        window.confirm = customConfirm;
        window.confirmSubmit = confirmSubmit;
    


    lucide.createIcons();
    const loginForm = document.getElementById('login-form');
    const loginBtn = document.getElementById('login-btn');
    const passkeyLoginBtn = document.getElementById('passkey-login-btn');
    const flashContainer = document.getElementById('flash-container');
    
    let captchaSolved = false;
    
    function enableButtons() {
        if (captchaSolved) {
            loginBtn.disabled = false;
            loginBtn.classList.remove('btn-disabled');
            if (passkeyLoginBtn) {
                passkeyLoginBtn.disabled = false;
                passkeyLoginBtn.classList.remove('btn-disabled');
            }
        }
    }
    
    function disableButtons() {
        captchaSolved = false;
        loginBtn.disabled = true;
        loginBtn.classList.add('btn-disabled');
        if (passkeyLoginBtn) {
            passkeyLoginBtn.disabled = true;
            passkeyLoginBtn.classList.add('btn-disabled');
        }
    }

    window.onTurnstileSuccess = function() {
        captchaSolved = true;
        enableButtons();
    };
    
    window.onTurnstileError = function() {
        disableButtons();
    };
    
    window.addEventListener('load', () => {
        setTimeout(() => {
            if (typeof turnstile !== 'undefined') {
                const turnstileEl = document.getElementById('turnstile-login');
                if (turnstileEl) {
                    const token = turnstile.getResponse('#turnstile-login');
                    if (token) {
                        captchaSolved = true;
                        enableButtons();
                    }
                }
            }
        }, 1000);
    });

    function showFlash(message, type = 'error') {
        const cls = type === 'error' ? 'flash-error' : 'flash-success';
        flashContainer.innerHTML = `<div class="flash-msg ${cls}"><i data-lucide="alert-circle" style="width:18px"></i><span>${message}</span></div>`;
        lucide.createIcons();
    }

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        const catid = document.getElementById('catid').value.trim();
        const otp = document.getElementById('otp').value.trim();
        
        if (!catid) {
            showFlash('Please enter your CatID');
            return;
        }

        if (!captchaSolved) {
            showFlash('Please complete the security verification');
            return;
        }

        let turnstileToken = '';
        try {
            if (typeof turnstile === 'undefined') {
                showFlash('Security verification widget not loaded. Please refresh the page.');
                return;
            }
            
            const turnstileElement = document.getElementById('turnstile-login');
            if (!turnstileElement) {
                showFlash('Security verification widget not found. Please refresh the page.');
                return;
            }
            
            turnstileToken = turnstile.getResponse('#turnstile-login');
            
            if (!turnstileToken) {
                showFlash('Please complete the security verification');
                captchaSolved = false;
                disableButtons();
                return;
            }
        } catch (err) {
            showFlash('Security verification failed. Please refresh the page and try again.');
            captchaSolved = false;
            disableButtons();
            return;
        }

        loginBtn.classList.add('loading');
        loginBtn.disabled = true;

        const payload = {
            cat_id: catid,
            otp_code: otp,
            token: turnstileToken
        };
        
        // Check if passkey 2FA is being used
        const usePasskey2FA = sessionStorage.getItem('use_passkey_2fa') === 'true';
        if (usePasskey2FA) {
            const passkeyData = sessionStorage.getItem('passkey_2fa_data');
            if (passkeyData) {
                payload.passkey_token = JSON.parse(passkeyData);
                payload.otp_code = ''; // Clear OTP if using passkey
                sessionStorage.removeItem('use_passkey_2fa');
                sessionStorage.removeItem('passkey_2fa_data');
            }
        }

        try {
            const response = await fetch('/api/login/catid', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            
            let data;
            try {
                data = await response.json();
            } catch (parseErr) {
                showFlash(`Server error: ${response.status} ${response.statusText}`);
                try {
                    turnstile.reset('#turnstile-login');
                } catch (resetErr) {}
                return;
            }
            
            if (!response.ok) {
                const errorMsg = data.error || data.message || `Server error: ${response.status}`;
                showFlash(errorMsg);
                
                // Show passkey 2FA option if 2FA is required
                if (errorMsg.includes('2FA is enabled') || errorMsg.includes('OTP code')) {
                    const usePasskey2FABtn = document.getElementById('use-passkey-2fa');
                    if (usePasskey2FABtn) {
                        usePasskey2FABtn.style.display = 'flex';
                    }
                }
                
                try {
                    if (typeof turnstile !== 'undefined') {
                        turnstile.reset('#turnstile-login');
                    }
                } catch (resetErr) {}
                captchaSolved = false;
                disableButtons();
                return;
            }

            if (data.success) {
                window.location.href = data.redirect_url || '/dashboard';
            } else {
                showFlash(data.error || 'Login failed');
                try {
                    if (typeof turnstile !== 'undefined') {
                        turnstile.reset('#turnstile-login');
                    }
                } catch (resetErr) {}
                captchaSolved = false;
                disableButtons();
            }
        } catch (err) {
            if (err.name === 'TypeError' && err.message.includes('fetch')) {
                showFlash('Connection error. Please check your internet connection and try again.');
            } else {
                showFlash(err.message || 'An unexpected error occurred. Please try again.');
            }
            try {
                if (typeof turnstile !== 'undefined') {
                    turnstile.reset('#turnstile-login');
                }
            } catch (resetErr) {}
            captchaSolved = false;
            disableButtons();
        } finally {
            loginBtn.classList.remove('loading');
            if (!captchaSolved) {
                loginBtn.disabled = true;
                loginBtn.classList.add('btn-disabled');
            }
        }
    });

    const htmlElement = document.documentElement;
    const updateTurnstileTheme = () => {
        const theme = htmlElement.getAttribute('data-theme') || 'dark';
        const turnstileEl = document.getElementById('turnstile-login');
        if (turnstileEl) {
            turnstileEl.setAttribute('data-theme', theme);
        }
    };
    
    const observer = new MutationObserver(updateTurnstileTheme);
    observer.observe(htmlElement, { attributes: true, attributeFilter: ['data-theme'] });
    updateTurnstileTheme();

    if (passkeyLoginBtn) {
        passkeyLoginBtn.addEventListener('click', async () => {
            if (!captchaSolved) {
                showFlash('Please complete the security verification');
                return;
            }
            
            const catid = document.getElementById('catid').value.trim();

            passkeyLoginBtn.disabled = true;
            passkeyLoginBtn.classList.add('loading');
            const originalHTML = passkeyLoginBtn.innerHTML;
            passkeyLoginBtn.innerHTML = '<div class="spinner"></div><span>Authenticating...</span>';

            try {
                const challengeResponse = await fetch('/api/passkey/authenticate/challenge', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(catid ? { cat_id: catid } : {})
                });

                const challengeData = await challengeResponse.json();
                if (!challengeData.success) {
                    showFlash(challengeData.error || 'Failed to initiate passkey authentication');
                    passkeyLoginBtn.disabled = !captchaSolved;
                    passkeyLoginBtn.classList.remove('loading');
                    passkeyLoginBtn.innerHTML = '<i data-lucide="fingerprint"></i> Login with Passkey';
                    if (!captchaSolved) {
                        passkeyLoginBtn.classList.add('btn-disabled');
                    }
                    lucide.createIcons();
                    return;
                }

                const challenge = Uint8Array.from(atob(challengeData.challenge.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));
                
                const publicKeyOptions = {
                    challenge: challenge,
                    timeout: challengeData.timeout || 60000
                };
                
                if (challengeData.allowCredentials && challengeData.allowCredentials.length > 0) {
                    publicKeyOptions.allowCredentials = challengeData.allowCredentials.map(cred => ({
                        ...cred,
                        id: Uint8Array.from(atob(cred.id.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0))
                    }));
                }

                const credential = await navigator.credentials.get({
                    publicKey: publicKeyOptions
                });

                const response = {
                    id: credential.id,
                    rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                    response: {
                        authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                        clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                        signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                        userHandle: credential.response.userHandle ? btoa(String.fromCharCode(...new Uint8Array(credential.response.userHandle))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '') : null
                    },
                    type: credential.type
                };

                const verifyResponse = await fetch('/api/passkey/authenticate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(response)
                });

                const verifyData = await verifyResponse.json();
                if (verifyData.success) {
                    window.location.href = verifyData.redirect_url || '/dashboard';
                } else {
                    showFlash(verifyData.error || 'Passkey authentication failed');
                    passkeyLoginBtn.disabled = !captchaSolved;
                    passkeyLoginBtn.classList.remove('loading');
                    passkeyLoginBtn.innerHTML = '<i data-lucide="fingerprint"></i> Login with Passkey';
                    if (!captchaSolved) {
                        passkeyLoginBtn.classList.add('btn-disabled');
                    }
                    lucide.createIcons();
                }
            } catch (err) {
                if (err.name === 'NotAllowedError') {
                    showFlash('Passkey authentication was cancelled');
                } else if (err.name === 'NotSupportedError') {
                    showFlash('Passkeys are not supported on this device');
                } else {
                    showFlash('Passkey authentication failed: ' + err.message);
                }
                passkeyLoginBtn.disabled = !captchaSolved;
                passkeyLoginBtn.classList.remove('loading');
                passkeyLoginBtn.innerHTML = '<i data-lucide="fingerprint"></i> Login with Passkey';
                if (!captchaSolved) {
                    passkeyLoginBtn.classList.add('btn-disabled');
                }
                lucide.createIcons();
            }
        });
    }

    // Passkey 2FA handler
    const usePasskey2FABtn = document.getElementById('use-passkey-2fa');
    if (usePasskey2FABtn) {
        usePasskey2FABtn.addEventListener('click', async () => {
            if (!captchaSolved) {
                showFlash('Please complete the security verification');
                return;
            }
            
            const catid = document.getElementById('catid').value.trim();
            if (!catid) {
                showFlash('Please enter your CatID first');
                return;
            }

            usePasskey2FABtn.disabled = true;
            usePasskey2FABtn.innerHTML = '<div class="spinner"></div><span>Authenticating...</span>';

            try {
                // Get passkey challenge for 2FA
                const challengeResponse = await fetch('/api/passkey/authenticate/challenge', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ cat_id: catid })
                });

                const challengeData = await challengeResponse.json();
                if (!challengeData.success) {
                    showFlash(challengeData.error || 'Failed to initiate passkey 2FA');
                    usePasskey2FABtn.disabled = false;
                    usePasskey2FABtn.innerHTML = '<i data-lucide="fingerprint"></i> Use Passkey for 2FA';
                    lucide.createIcons();
                    return;
                }

                // Convert challenge for WebAuthn API
                const challenge = Uint8Array.from(atob(challengeData.challenge.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));
                
                const publicKeyOptions = {
                    challenge: challenge,
                    timeout: challengeData.timeout || 60000
                };
                
                if (challengeData.allowCredentials && challengeData.allowCredentials.length > 0) {
                    publicKeyOptions.allowCredentials = challengeData.allowCredentials.map(cred => ({
                        ...cred,
                        id: Uint8Array.from(atob(cred.id.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0))
                    }));
                }

                // Call WebAuthn API
                const credential = await navigator.credentials.get({
                    publicKey: publicKeyOptions
                });

                // Prepare response
                const passkeyResponse = {
                    id: credential.id,
                    rawId: btoa(String.fromCharCode(...new Uint8Array(credential.rawId))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                    response: {
                        authenticatorData: btoa(String.fromCharCode(...new Uint8Array(credential.response.authenticatorData))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                        clientDataJSON: btoa(String.fromCharCode(...new Uint8Array(credential.response.clientDataJSON))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                        signature: btoa(String.fromCharCode(...new Uint8Array(credential.response.signature))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, ''),
                        userHandle: credential.response.userHandle ? btoa(String.fromCharCode(...new Uint8Array(credential.response.userHandle))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '') : null
                    },
                    type: credential.type
                };

                // Store passkey data and retry login
                sessionStorage.setItem('use_passkey_2fa', 'true');
                sessionStorage.setItem('passkey_2fa_data', JSON.stringify(passkeyResponse));
                
                // Trigger login form submission
                usePasskey2FABtn.innerHTML = '<i data-lucide="fingerprint"></i> Use Passkey for 2FA';
                lucide.createIcons();
                
                // Submit the login form
                loginForm.dispatchEvent(new Event('submit'));
                
            } catch (err) {
                console.error('Passkey 2FA error:', err);
                if (err.name === 'NotAllowedError') {
                    showFlash('Passkey 2FA was cancelled');
                } else if (err.name === 'NotSupportedError') {
                    showFlash('Passkeys are not supported on this device');
                } else {
                    showFlash('Passkey 2FA failed: ' + err.message);
                }
                usePasskey2FABtn.disabled = false;
                usePasskey2FABtn.innerHTML = '<i data-lucide="fingerprint"></i> Use Passkey for 2FA';
                lucide.createIcons();
            }
        });
    }
