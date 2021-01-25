import configparser as cfg
import requests


class Bot:

    def __init__(self):
        self.config = "../config.cfg"
        self.token = self.get_token_from_config()
        self.base = "https://api.telegram.org/bot{}/".format(self.token)

    def get_token_from_config(self):
        parser = cfg.ConfigParser()
        parser.read(self.config)
        return parser.get('api_key', 'token')

    def send_message(self, message_content):
        url = self.base + message_content
        if message_content is not None:
            requests.get(url)


def send_text_message(msg, chat_id):
    bot = Bot()
    message_content = "sendMessage?chat_id={}&text={}".format(chat_id, msg)
    bot.send_message(message_content)

