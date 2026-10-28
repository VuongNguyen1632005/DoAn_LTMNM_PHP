const fs=require('fs'),path=require('path'),crypto=require('crypto');
const root=path.resolve(__dirname,'../..'),out=path.join(root,'docs/figures');
const env=Object.fromEntries(fs.readFileSync(path.join(root,'.env'),'utf8').split(/\r?\n/).filter(x=>/^[A-Z_]+=/.test(x)).map(x=>{let i=x.indexOf('=');return [x.slice(0,i),x.slice(i+1).replace(/^(['"])(.*)\1$/,'$2')]}));
const base='http://127.0.0.1:5984',db='retail_order_delivery_demo';
async function api(p='',method='GET',body){
 const target=decodeURIComponent(p.split('/')[0]);
 if(target===db&&method!=='GET'&&!(method==='POST'&&['/_find','/_explain','/_all_docs'].some(s=>p.startsWith(db+s))))throw Error('Main demo database is read-only');
 if(method!=='GET'&&!target.startsWith('_')&&target!==db&&!target.endsWith('_scratch')&&target!=='retail_ui_capture_demo')throw Error('Writes require capture scratch database');
 if(p==='_replicate'&&body?.target){const t=typeof body.target==='string'?body.target:body.target.url;const name=t.startsWith('http')?new URL(t).pathname.split('/').at(-1):t;if(!name.endsWith('_scratch'))throw Error('Replication target must be scratch');}
 const r=await fetch(base+'/'+p,{method,headers:{Authorization:'Basic '+Buffer.from(env.COUCHDB_USER+':'+env.COUCHDB_PASSWORD).toString('base64'),'Content-Type':'application/json'},body:body===undefined?undefined:JSON.stringify(body)});let data=await r.json();if(!r.ok)throw Error(r.status+' '+JSON.stringify(data));return data;}
function canonical(v){if(Array.isArray(v))return v.map(canonical);if(v&&typeof v==='object')return Object.fromEntries(Object.keys(v).filter(k=>k!=='_rev').sort().map(k=>[k,canonical(v[k])]));return v;}
const hash=v=>crypto.createHash('sha256').update(v).digest('hex');
function save(name,data){fs.mkdirSync(path.join(out,'logs'),{recursive:true});fs.writeFileSync(path.join(out,'logs',name),JSON.stringify(data,null,2));}
async function fingerprint(){const info=await api(db),all=await api(db+'/_all_docs?include_docs=true');return {doc_count:info.doc_count,update_seq:info.update_seq,sha256:hash(JSON.stringify(canonical(all.rows.map(r=>r.doc)))),algorithm:'SHA-256 sorted all_docs, recursive sorted keys, _rev omitted'};}
async function browser(){const {chromium}=require('playwright');const b=await chromium.launch({headless:true});const c=await b.newContext({viewport:{width:1440,height:900},deviceScaleFactor:2,locale:'en-US'});await c.request.post(base+'/_session',{form:{name:env.COUCHDB_USER,password:env.COUCHDB_PASSWORD}});fs.mkdirSync(path.join(__dirname,'.auth'),{recursive:true});await c.storageState({path:path.join(__dirname,'.auth/fauxton.json')});return {b,c,p:await c.newPage()};}
module.exports={fs,path,root,out,env,base,db,api,save,hash,fingerprint,browser};
