import sys
import os
import json
import codecs

pathFile = os.path.dirname(__file__)

#append the relative location you want to import from
sys.path.append(os.path.abspath(os.path.join(pathFile, "lib/")))

import MyLib
import MyLogger

#Déclaration systeme
arguments = dict() #dict
i = 0 #Int
config_json = {} #Json

#####################################################################
#Les definitions

#Procedure Installation
def install() :
    MyLogger.logger.info("Lance l'installation.")
    try:
        if arguments["mode"] == "full" or arguments["mode"] == "client" :
            MyLogger.logger.info("Install client")
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaClient/Deploy/",config_json_project["client"]["path"],True,"")

            MyLogger.logger.info("Personnalisation des fichiers clients.")
            for cur_fichier in config_json_private["client"]["install"]["fileToCusto"]:
                MyLogger.logger.debug("Edition du fichier : "+cur_fichier["pathInProject"]+cur_fichier["file"])
                MyLib.editFile(config_json_project["client"]["path"] + cur_fichier["pathInProject"] + cur_fichier["file"], config_json_project["client"]["install"]["values"])
            
            MyLogger.logger.info("Install client API")
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaClient/Api/",config_json_project["client"]["path"]+"/API/",True,"")

        if arguments["mode"] == "full" or arguments["mode"] == "server" :
            MyLogger.logger.info("Install server")
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaServer/Deploy/",config_json_project["server"]["path"],True,"")

            MyLogger.logger.info("Personnalisation des fichiers server.")
            for cur_fichier in config_json_private["server"]["install"]["fileToCusto"]:
                MyLogger.logger.debug("Edition du fichier : "+cur_fichier["pathInProject"]+cur_fichier["file"])
                MyLib.editFile(config_json_project["server"]["path"] + cur_fichier["pathInProject"] + cur_fichier["file"], config_json_project["server"]["install"]["values"])
            
            MyLogger.logger.info("Install server API")
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaServer/Api/",config_json_project["server"]["path"]+"/API/",True,"")
            
    except Exception as e:
        MyLogger.logger.error("Erreur pendant install : ("+format(e)+")")
        sys.exit("Erreur")
    
#Procedure Mise à jour
def update() :
    MyLogger.logger.info("Lance la mise à jour.")
    try:
        if arguments["mode"] == "full" or arguments["mode"] == "client" :
            MyLogger.logger.info("Update client")
            mergedListNoUpdate = config_json_project["client"]["update"]["noUpdate"] + config_json_private["client"]["update"]["noUpdate"]
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaClient/Deploy/",config_json_project["client"]["path"],True,mergedListNoUpdate)
            
            MyLogger.logger.info("Update client")
            mergedListNoUpdate = config_json_project["client"]["update"]["noUpdate"] + config_json_private["client"]["update"]["noUpdate"]
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaClient/Api/",config_json_project["client"]["path"]+"/API/",True,"")
        

        if arguments["mode"] == "full" or arguments["mode"] == "server" :
            MyLogger.logger.info("Update server")
            mergedListNoUpdate = config_json_project["server"]["update"]["noUpdate"] + config_json_private["server"]["update"]["noUpdate"]
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaServer/Deploy/",config_json_project["server"]["path"],True,mergedListNoUpdate)
            
            MyLogger.logger.info("Update server")
            mergedListNoUpdate = config_json_project["server"]["update"]["noUpdate"] + config_json_private["server"]["update"]["noUpdate"]
            MyLib.copytree(config_json_project["pathToFw"]+"/OdaServer/Api/",config_json_project["server"]["path"]+"/API/",True,"")
            
    except Exception as e:
        MyLogger.logger.error("Erreur pendant update : ("+format(e)+")")
        sys.exit("Erreur")

#Procedure Say More
def more() :
    MyLogger.logger.info("Les options disponible sont : install,update,more.")
    MyLogger.logger.info("Exemple de syntax pour 'install' : 'python deploy.py install full exemple.config.project.json '.")
    MyLogger.logger.info("Exemple de syntax pour 'update' : 'python deploy.py update full exemple.config.project.json '.")
    MyLogger.logger.info("Exemple de syntax pour 'more' : 'python deploy.py more'.")

#####################################################################
#Le programme

#Message de bienvenu.
MyLogger.logger.info ("-------------------------------------------------------")
MyLogger.logger.info ("Welcome in the deployement module for ODA FrameWork .")

#Récupération des arguments.
arguments["action"] = "more"
for x in sys.argv :
    i += 1
    if i == 2 :
        arguments["action"] = x
        if x not in ["install", "update", "more"] :
            MyLogger.logger.warning("Votre argument ("+x+") est incorrect, seul 'install','update','more' sont aurorisés.")
            sys.exit("Erreur")
        else :
            MyLogger.logger.info("Mode d'action choisi : "+x+".")
    if i == 3 :
        arguments["mode"] = x
        if x not in ["client", "server", "full"] :
            MyLogger.logger.warning("Votre argument ("+x+") est incorrect, seul 'client','server','full' sont aurorisés.")
            sys.exit("Erreur")
        else :
            MyLogger.logger.info("Mode d'action choisi : "+x+".")
    elif i == 4 :
        if ".json" not in x:
            MyLogger.logger.warning("Votre argument est ("+x+") est incorrect, un fichier de configuration .json est attendu.")
            sys.exit("Erreur")
        else :
            arguments["fichier_conf"] = x
            #Initialisation
            config_json_private = MyLib.charger_config(pathFile + "/" + "private.config.odafw.json")
            config_json_project = MyLib.charger_config(arguments["fichier_conf"])

#Affichage
if arguments["action"] == "install" :
    install()
elif arguments["action"] == "update" :
    update()
elif arguments["action"] == "more" :
    more()

#Message de fin.
MyLogger.logger.info ("Fin du script.")
MyLogger.logger.info ("-------------------------------------------------------")
sys.exit(0)
