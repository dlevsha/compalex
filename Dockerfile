FROM php:7.4-cli

RUN apt-get update && apt-get install -y  \
freetds-bin \
freetds-dev \
freetds-common \
libpq-dev \
libsybdb5 \
alien \
libaio1 \
tdsodbc

RUN ln -s /usr/lib/x86_64-linux-gnu/libsybdb.so /usr/lib/ && ln -s /usr/lib/x86_64-linux-gnu/libsybdb.a /usr/lib/
RUN  docker-php-ext-install pdo pdo_dblib && docker-php-ext-enable pdo_dblib

RUN docker-php-ext-install -j$(nproc) iconv pdo pdo_mysql pdo_pgsql

# For Oracle support visit https://www.oracle.com/database/technologies/instant-client/linux-x86-64-downloads.html
RUN mkdir -p /var/oracle/clients/ \
    && curl https://download.oracle.com/otn_software/linux/instantclient/oracle-instantclient-basiclite-linuxx64.rpm --output /var/oracle/clients/oracle-instantclient-basiclite-linuxx64.rpm \
    && curl https://download.oracle.com/otn_software/linux/instantclient/oracle-instantclient-devel-linuxx64.rpm --output /var/oracle/clients/oracle-instantclient-devel-linuxx64.rpm

RUN cd /var/oracle/clients/ \
    && BASIC=$(alien -d  /var/oracle/clients/oracle-instantclient-basiclite-linuxx64.rpm | cut -d' ' -f1) \
    && DEVEL=$(alien -d  /var/oracle/clients/oracle-instantclient-devel-linuxx64.rpm | cut -d' ' -f1) \
    && dpkg -i $BASIC \
    && dpkg -i $DEVEL

RUN pecl install oci8 \
    && CURRENT_ORACLE_CLIENT_VERSION=$(ls /usr/lib/oracle/) \
    && export LD_LIBRARY_PATH=/usr/lib/oracle/${CURRENT_ORACLE_CLIENT_VERSION}/client64/lib/${LD_LIBRARY_PATH:+:$LD_LIBRARY_PATH} \
    && docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/usr/lib/oracle/${CURRENT_ORACLE_CLIENT_VERSION}/client64/lib,${CURRENT_ORACLE_CLIENT_VERSION} \
    && docker-php-ext-install pdo_oci \
    && docker-php-ext-install oci8

# -------

COPY . /usr/src/compalex

WORKDIR /usr/src/compalex

CMD [ "php", "-S", "0.0.0.0:8000" ]