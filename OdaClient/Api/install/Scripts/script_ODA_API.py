import sys
import os
import json
import codecs

#append the relative location you want to import from
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "../../python/")))

#import your module stored in '../common'
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
    MyLogger.logger.info("+Lance l'installation.")
    try:
        MyLogger.logger.info("+Etape 1 : copie des fichiers.")
        MyLib.copytree(config_json["parametres"]["cheminWWW"]+config_json["parametres"]["cheminAPI"]+"deploy",config_json["parametres"]["cheminWWW"],True,"","")
            
        MyLogger.logger.info("+Etape 2 : personnalisation des fichiers.")
        for cur_fichier in config_json["fichiers"]:
            MyLogger.logger.debug("-Edition du fichier : "+cur_fichier["chemin"]+cur_fichier["nom"])
            MyLib.editFile(config_json["parametres"]["cheminWWW"] + cur_fichier["chemin"] + cur_fichier["nom"], config_json["valeurs"])
                
    except Exception as e:
        MyLogger.logger.error("Erreur pendant install : ("+format(e)+")")
        sys.exit("Erreur")
    
#Procedure Mise à jour
def update() :
    MyLogger.logger.info("+Lance la mise à jour.")
    try:
        MyLogger.logger.info("+Update des fichiers.")
        MyLib.copytree(config_json["parametres"]["cheminWWW"]+config_json["parametres"]["cheminAPI"]+"deploy",config_json["parametres"]["cheminWWW"],True,config_json["noUpdate"],config_json["parametres"])
                
    except Exception as e:
        MyLogger.logger.error("Erreur pendant update : ("+format(e)+")")
        sys.exit("Erreur")

#Procedure Say More
def more() :
    MyLogger.logger.info("+Les options disponible sont : install,update,more.")
    MyLogger.logger.info("+Exemple de syntax pour 'install' : 'python script_ODA_API.py exemple.config.api.oda.json install'.")
    MyLogger.logger.info("+Exemple de syntax pour 'update' : 'python script_ODA_API.py exemple.config.api.oda.json update'.")
    MyLogger.logger.info("+Exemple de syntax pour 'more' : 'python script_ODA_API.py more'.")

#####################################################################
#Le programme

#Message de bienvenu.
MyLogger.logger.info ("-------------------------------------------------------")
MyLogger.logger.info ("+Bienvenue dans le script de traitement ODA module API.")

#Récupération des arguments.
for x in sys.argv :
    i += 1
    if i == 2 :
        if ".oda.json" not in x:
            MyLogger.logger.warning("+Votre second argument est ("+x+") est incorrect, un fichier de configuration .api.oda.json est attendu.")
            sys.exit("Erreur")
        else :
            arguments["fichier_conf"] = x
    elif i == 3 :
        arguments["action"] = x
        if x not in ["install", "update", "more"] :
            MyLogger.logger.warning("+Votre premier argument ("+x+") est incorrect, seul 'install','update','more' sont aurorisés.")
            sys.exit("Erreur")
        else :
            MyLogger.logger.info("+Mode d'action choisi : "+x+".")
    if len(arguments) == 0 :
        arguments["action"] = "more"

#Initialisation
config_json = MyLib.charger_config(arguments["fichier_conf"])    

#Affichage
if arguments["action"] == "install" :
    install()
elif arguments["action"] == "update" :
    update()
elif arguments["action"] == "more" :
    more()

#Message de fin.
MyLogger.logger.info ("+Fin du script.")
sys.exit(0)

    
