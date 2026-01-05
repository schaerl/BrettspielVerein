FROM liquibase/liquibase:4.33
RUN lpm add mysql --global

# For liuqibase to be able to write to the mounted folders, the user needs the correct priviliges
ARG USER_ID
USER root
RUN usermod -u ${USER_ID} liquibase
USER liquibase
