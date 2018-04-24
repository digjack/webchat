import itchat,sys,redis,time,threading,json,os
from itchat.content import *

class worker(object):
    def __init__(self, session_id):
        self.session_id = session_id

    def lc(self):
        print('login success')
    def ec(self):
        print('exit')
    def process(self):
        session_id = self.session_id
        out = "current id {id}"
    
        print(out.format(id=session_id))
        lc = self.lc()
        ec = self.ec()
        infopath = os.path.abspath(''.join(['/data/webchat/web/public/info/', session_id.decode("utf-8")]))
       
        print(infopath)
        if not os.path.exists(infopath):
             os.makedirs(infopath)

        r = redis.StrictRedis(host='localhost', port=6379, db=0)
        qrpath = ''.join([infopath, '/qr.png'])
        
        info={'qrfile': qrpath}
        infojson = json.dumps(info,  ensure_ascii=False)
        r.set(session_id, infojson)
        itchat.auto_login(loginCallback=lc, exitCallback=ec, picDir=qrpath)
        memberList = itchat.get_friends()[1:]
        
        infojson = r.get(session_id);
        info = json.loads(infojson.decode("utf-8"))
       
        info['friends'] = memberList 
        r.set(session_id, json.dumps(info), 86400)


session_id = sys.argv[1] 
worker = worker(session_id)
worker.process()


