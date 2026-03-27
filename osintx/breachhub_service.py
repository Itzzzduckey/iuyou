import asyncio
import aiohttp
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import uvicorn

API_KEY  = "TJQPHHRg5kfiQ5dbwTNo5sOuXPHKGv"
BASE_URL = "https://breachhub.org"
PORT     = 5555
HOST     = "127.0.0.1"

app = FastAPI(title="BreachHub Microservice")

class SearchRequest(BaseModel):
    email: str
    timeout: int = 180

def build_url(path, **params):
    params["key"] = API_KEY
    qs = "&".join(f"{k}={v}" for k, v in params.items())
    return f"{BASE_URL}{path}?{qs}"

async def _get(session, path, **params):
    try:
        async with session.get(build_url(path, **params)) as r:
            text = await r.text()
            if not text.strip():
                return {"error": "empty response"}
            import json
            return json.loads(text)
    except Exception as e:
        return {"error": str(e)}

# ── TUTTI GLI ENDPOINT EMAIL ──────────────────────────────────────────────

async def search_snusbase(s, email):
    return await _get(s, "/api/snusbase", query=email)

async def search_leakcheck(s, email):
    return await _get(s, "/api/leakcheck/v2", query=email)

async def search_leakosint(s, email):
    return await _get(s, "/api/leakosint", query=email)

async def search_breachbase(s, email):
    return await _get(s, "/api/breachbase", category="email", term=email)

async def search_intelvault(s, email):
    return await _get(s, "/api/intelvault", email=email)

async def search_breachdirectory(s, email):
    return await _get(s, "/api/breachdirectory", type="email", query=email)

async def search_hackcheck(s, email):
    return await _get(s, "/api/hackcheck", category="email", term=email)

async def search_osintkit(s, email):
    return await _get(s, "/api/osintkit", category="email", term=email)

async def search_breachvip(s, email):
    return await _get(s, "/api/breachvip", category="email", query=email)

async def search_breachrip_db(s, email):
    return await _get(s, "/api/breachrip/db", type="email", term=email)

async def search_breachrip_amazon(s, email):
    return await _get(s, "/api/breachrip/amazon", type="email", term=email)

async def search_melissa(s, email):
    return await _get(s, "/api/melissa", input=email)

async def search_intelbase_email(s, email):
    return await _get(s, "/api/intelbase/email/check", term=email)

async def search_stealerlogs(s, email):
    return await _get(s, "/api/stealerlogs/search", type="email", query=email)

async def search_breachlookup(s, email):
    return await _get(s, "/api/breachlookup", query=email)

async def search_osintcat(s, email):
    return await _get(s, "/api/osintcat/database-search", query=email)

async def search_xosint(s, email):
    return await _get(s, "/api/xosint/search", query=email)

async def search_camelhub(s, email):
    return await _get(s, "/api/camelhub", query=email)

async def search_infodra(s, email):
    return await _get(s, "/api/infodra", category="login", query=email)

async def search_netium_db(s, email):
    return await _get(s, "/api/netium/search/database", query=email)

async def search_netium_email(s, email):
    return await _get(s, "/api/netium/search/email", email=email)

async def search_netium_stealerlogs(s, email):
    return await _get(s, "/api/netium/search/stealer-logs", query=email)

async def search_seekbase(s, email):
    return await _get(s, "/api/seekbase/search", query=email)

async def search_wentyn(s, email):
    return await _get(s, "/api/wentyn", category="email", term=email)

async def search_hudsonrock(s, email):
    return await _get(s, "/api/hudsonrock", email=email)

async def search_akula(s, email):
    return await _get(s, "/api/akula", category="email", term=email)

async def search_leaksight(s, email):
    return await _get(s, "/api/leaksight", type="username", query=email)

async def search_leaksight_searchstring(s, email):
    return await _get(s, "/api/leaksight", type="searchstring", query=email)

async def search_seon_email(s, email):
    return await _get(s, "/api/seon/email", email=email)

async def search_seon_verification(s, email):
    return await _get(s, "/api/seon/email-verification", email=email)

async def search_oathnet_breach(s, email):
    return await _get(s, "/api/oathnet/breach", query=email)

async def search_oathnet_stealer(s, email):
    return await _get(s, "/api/oathnet/stealer", query=email)

async def search_inf0sec_leaks(s, email):
    return await _get(s, "/api/inf0sec", module="leaks", query=email)

async def search_inf0sec_username(s, email):
    return await _get(s, "/api/inf0sec", module="username", query=email)

async def search_fetchbase(s, email):
    return await _get(s, "/api/intelfetch/fetchbase", query=email)

async def search_github(s, email):
    return await _get(s, "/api/github", email=email)

async def search_crowsint(s, email):
    return await _get(s, "/api/crowsint", query=email)

async def search_indicia_email(s, email):
    return await _get(s, "/api/indicia/email", query=email)

async def search_indicia_leakcheck(s, email):
    return await _get(s, "/api/indicia/leakcheck", query=email, type="auto")

async def search_indicia_web_dbs(s, email):
    svcs = "breachvip,snusbase,leakcheck,breachbase,intelvault,hackcheck,breachdirectory,stealerlogs"
    return await _get(s, "/api/indicia/web-dbs", query=email, services=svcs)

async def search_indicia_gmail(s, email):
    if not email.lower().endswith("@gmail.com"):
        return {"skipped": True}
    return await _get(s, "/api/indicia/gmail", query=email)

# username part of email (prima della @)
async def search_breachlookup_user(s, email):
    user = email.split("@")[0]
    return await _get(s, "/api/breachlookup", query=user)

async def search_breachbase_user(s, email):
    user = email.split("@")[0]
    return await _get(s, "/api/breachbase", category="username", term=user)

async def search_hackcheck_user(s, email):
    user = email.split("@")[0]
    return await _get(s, "/api/hackcheck", category="username", term=user)

async def search_snusbase_user(s, email):
    user = email.split("@")[0]
    return await _get(s, "/api/snusbase", query=user)

ENDPOINTS = {
    # Email diretta
    "snusbase":               search_snusbase,
    "leakcheck":              search_leakcheck,
    "leakosint":              search_leakosint,
    "breachbase":             search_breachbase,
    "intelvault":             search_intelvault,
    "breachdirectory":        search_breachdirectory,
    "hackcheck":              search_hackcheck,
    "osintkit":               search_osintkit,
    "breachvip":              search_breachvip,
    "breachrip_db":           search_breachrip_db,
    "breachrip_amazon":       search_breachrip_amazon,
    "melissa":                search_melissa,
    "intelbase_email":        search_intelbase_email,
    "stealerlogs":            search_stealerlogs,
    "breachlookup":           search_breachlookup,
    "osintcat":               search_osintcat,
    "xosint":                 search_xosint,
    "camelhub":               search_camelhub,
    "infodra":                search_infodra,
    "netium_db":              search_netium_db,
    "netium_email":           search_netium_email,
    "netium_stealerlogs":     search_netium_stealerlogs,
    "seekbase":               search_seekbase,
    "wentyn":                 search_wentyn,
    "hudsonrock":             search_hudsonrock,
    "akula":                  search_akula,
    "leaksight":              search_leaksight,
    "leaksight_searchstring": search_leaksight_searchstring,
    "seon_email":             search_seon_email,
    "seon_verification":      search_seon_verification,
    "oathnet_breach":         search_oathnet_breach,
    "oathnet_stealer":        search_oathnet_stealer,
    "inf0sec_leaks":          search_inf0sec_leaks,
    "inf0sec_username":       search_inf0sec_username,
    "fetchbase":              search_fetchbase,
    "github":                 search_github,
    "crowsint":               search_crowsint,
    "indicia_email":          search_indicia_email,
    "indicia_leakcheck":      search_indicia_leakcheck,
    "indicia_web_dbs":        search_indicia_web_dbs,
    "indicia_gmail":          search_indicia_gmail,
    # Username (parte prima della @)
    "snusbase_user":          search_snusbase_user,
    "breachbase_user":        search_breachbase_user,
    "hackcheck_user":         search_hackcheck_user,
    "breachlookup_user":      search_breachlookup_user,
}

async def run_with_rate_limit(session, email):
    names = list(ENDPOINTS.keys())
    funcs = list(ENDPOINTS.values())
    results = {}
    batch_size = 3
    delay = 1.1  # ~2.7 req/sec, sotto limite 3/sec

    for i in range(0, len(names), batch_size):
        batch_names = names[i:i+batch_size]
        batch_funcs = funcs[i:i+batch_size]
        batch_results = await asyncio.gather(
            *[func(session, email) for func in batch_funcs],
            return_exceptions=True
        )
        for name, result in zip(batch_names, batch_results):
            results[name] = {"error": str(result)} if isinstance(result, Exception) else result
        if i + batch_size < len(names):
            await asyncio.sleep(delay)

    return results

@app.post("/search")
async def search_all(req: SearchRequest):
    if not req.email or "@" not in req.email:
        raise HTTPException(status_code=400, detail="Email non valida")
    timeout = aiohttp.ClientTimeout(total=req.timeout)
    async with aiohttp.ClientSession(timeout=timeout) as session:
        results = await run_with_rate_limit(session, req.email)
    return {"email": req.email, "results": results}

@app.get("/health")
def health():
    return {"status": "ok"}

if __name__ == "__main__":
    uvicorn.run("breachhub_service:app", host=HOST, port=PORT, reload=False)