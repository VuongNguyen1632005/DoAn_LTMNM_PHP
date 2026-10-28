const {fs,path,root,out,base,db,api,save,hash,fingerprint,browser}=require('./common.cjs');
let manifest=[];const mf=path.join(out,'manifest.json');if(fs.existsSync(mf))manifest=JSON.parse(fs.readFileSync(mf));
function persist(){const old=fs.existsSync(mf)?JSON.parse(fs.readFileSync(mf)):[];const byId=new Map(old.map(r=>[r.id,r]));for(const r of manifest)byId.set(r.id,r);fs.writeFileSync(mf,JSON.stringify([...byId.values()],null,2));}
async function shot(p,id,file,group,caption,extra={}){await p.waitForTimeout(600);const dest=path.join(out,file+'.png');await p.screenshot({path:dest,animations:'disabled'});const buf=fs.readFileSync(dest);const row={id,file:file+'.png',group,report_section:id.startsWith('F')?'Chương '+id[1]:'Ảnh bổ sung '+group,caption,source_url:p.url(),database:db,expected:null,actual:null,status:'OK',type:'gui',sha256:hash(buf),width:buf.readUInt32BE(16),height:buf.readUInt32BE(20),bytes:buf.length,redactions:[],...extra};manifest=(fs.existsSync(mf)?JSON.parse(fs.readFileSync(mf)):[]).filter(x=>x.id!==id);manifest.push(row);persist();console.log(id,row.status);return row;}
async function attempt(p,id,fn){for(let i=0;i<3;i++){try{return await fn(i)}catch(e){if(i===2)return await shot(p,id,id+'_FAILED','C','Không thể hoàn thành thao tác trên giao diện',{status:'FAILED',reason:e.message});await p.reload();await p.waitForTimeout(900);}}}
async function go(p,route){await p.goto(base+'/_utils/#'+route);await p.waitForLoadState('networkidle',{timeout:10000}).catch(()=>{});await p.waitForTimeout(500);}
async function editor(p,q){await p.locator('.ace_editor').first().waitFor();await p.evaluate(q=>document.querySelector('.ace_editor').env.editor.setValue(q,-1),q);}

module.exports={shot,attempt,go,editor};

