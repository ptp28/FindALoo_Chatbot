import logging


class LogManager:

    def __init__(self, logger_name):
        self.logger_name = logger_name

        logging.basicConfig(
            filename="logfile.log",
            format='%(asctime)s ::: %(name)s - %(levelname)s - %(message)s',
            filemode='a'
        )
        self.logger = logging.getLogger(self.logger_name)
        self.logger.setLevel(logging.DEBUG)


def log_error(name, message):
    log_manager = LogManager(name)
    log_manager.logger.error(message)


def log_info(name, message):
    log_manager = LogManager(name)
    log_manager.logger.info(message)


def log_warning(name, message):
    log_manager = LogManager(name)
    log_manager.logger.warning(message)
