import os, glob, time
import requests as r
import simplejson as json

'''al posto di user e password alle righe 11, 12, 13 e 72 inserire le proprie credenziali'''

os.environ['TZ'] = 'Italy'
time.tzset()

class qgisTransifex:
    def __init__(self, user, password, Italian):
        self.username = user
        self.password = password
        self.language = Italian
        self.transifexApi = 'https://www.transifex.com/api/2/project'
        self.availableApis = {'stats': 'stats'} 
        self.translateProjects = [
            {'name': 'gui', 'pslug': 'QGIS'},
            {'name': 'docs', 'pslug': 'qgis-documentation'},
            {'name': 'site', 'pslug': 'qgis-website'}
        ]
        
    def getResourcesList(self, pslug):
        req = r.get('/'.join([self.transifexApi, pslug]) + '?details', auth=(self.username, self.password))
        return json.loads(req.content)
    
    def getResourceStats(self, pslug, rslug):
        req = r.get('/'.join([self.transifexApi, pslug, 'resource', rslug, self.availableApis['stats'], self.language]), auth=(self.username, self.password))
        return json.loads(req.content)
    
    def getProjectStats(self, project, resources):
        stats = {}
        stats['trans_words'] = 0
        stats['untrans_words'] = 0
        stats['trans_entities'] = 0
        stats['untrans_entities'] = 0
        finalStats = {}
        finalStats['project'] = project['name']
        finalStats['updated_on'] = time.strftime('%d/%m/%Y %H:%M:%S')
        
        rlist = resources['resources']
        for resource in rlist:
            rstats = self.getResourceStats(project['pslug'], resource['slug'])
            stats['trans_words'] += rstats['translated_words']
            stats['untrans_words'] += rstats['untranslated_words']
            stats['trans_entities'] += rstats['translated_entities']
            stats['untrans_entities'] += rstats['untranslated_entities']
        
        stats['completed'] = float(stats['trans_entities']) / (stats['trans_entities'] + stats['untrans_entities']) * 100
        finalStats['stats'] = stats
        
        return finalStats
    
    def getGlobalStats(self):
        globalStats = []
        for proj in self.translateProjects:
            resources = self.getResourcesList(proj['pslug'])
            projStats = self.getProjectStats(proj, resources)
            globalStats.append(projStats)
        
        return globalStats
    
    def dumpJsonFile(self, path, stats, name):
        os.chdir(path)
        file = glob.glob(name)
        if len(file) > 0:
            os.remove(name)
        with open(name, 'w') as outfile:
            outfile.write(json.dumps(stats, indent=4, ensure_ascii=False))
        

qgisStats = qgisTransifex('user', 'password', 'Italian')
stats = qgisStats.getGlobalStats()
qgisStats.dumpJsonFile('path', stats, 'file.json')


''''''
