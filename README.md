# CassaNostra

A school project consisting in a web management software for supermarket chains. Click [here](https://qvanto.altervista.org/cassanostra) to try a live demo.  
Here's an overview: [presentation on Google Slides](https://docs.google.com/presentation/d/1BZqx6dF80AIc27ogn_DqtJSa6UI6lj_4B4i2HhSzXRg).

## Database structure

### Tables

#### Utente
Stores user details and credentials.
```sql
CREATE TABLE cnUtente (
  `Username` varchar(30) NOT NULL PRIMARY KEY,
  `Password` char(60) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Nome` varchar(50) NOT NULL,
  `Cognome` varchar(50) NOT NULL,
  `Ruolo` enum('MAG','DIR','CLI','CAS','FOR','ADM') NOT NULL,
  `Azienda` varchar(30) DEFAULT NULL,
)
```

#### CartaFedelta
Stores fidelity cards and their balances.
```sql
CREATE TABLE cnCartaFedelta (
  `ID_Carta` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `SaldoPunti` int(11) unsigned NOT NULL,
  `FK_Utente` varchar(30) UNIQUE NOT NULL,
  FOREIGN KEY (`FK_Utente`) REFERENCES `cnUtente` (`Username`);
)
```

#### PuntoVendita
Stores the names of the different stores.
```sql
CREATE TABLE cnPuntoVendita (
  `ID_PuntoVendita` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NomePunto` varchar(40) UNIQUE NOT NULL
)
```

#### Cassa
Stores cash registers IDs and the market they belong to.
```sql
CREATE TABLE cnCassa (
  `ID_Cassa` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NumeroCassa` int(11) NOT NULL,
  `FK_PuntoVendita` int(11) NOT NULL,
  FOREIGN KEY (`FK_PuntoVendita`) REFERENCES `cnPuntoVendita` (`ID_PuntoVendita`)
)
```

#### Fattura
Stores information about invoices made by or sent to the market. In the first case, the invoice entry is actually a receipt and the user FK will reference a client (if the client used his/her fidelity card) or null; otherwise, the user FK will reference a supplier.
```sql
CREATE TABLE cnFattura (
  `ID_Fattura` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NumeroFattura` int(11) unsigned NOT NULL,
  `DataFattura` date NOT NULL,
  `FK_Utente` varchar(30) DEFAULT NULL,
  `ScontrinoCassa` tinyint(1) NOT NULL,
  FOREIGN KEY (`FK_Utente`) REFERENCES `cnUtente` (`Username`)
)
```

#### Prodotto
Stores information about products, including their barcode and current sell price.
```sql
CREATE TABLE cnProdotto (
  `ID_Prodotto` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NomeProdotto` varchar(50) NOT NULL,
  `Produttore` varchar(30) NOT NULL,
  `EAN_Prodotto` char(13) UNIQUE NOT NULL,
  `PrezzoVenditaAttuale` decimal(6,2) unsigned NOT NULL,
)
```

#### Acquisto
Stores the entries of the invoices made by suppliers. These entries are used to calculate current inventory status and the amount of money spent for restocks.
```sql
CREATE TABLE cnAcquisto (
  `ID_Acquisto` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `DataOra` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Quantita` int(11) NOT NULL,
  `PrezzoAcquisto` decimal(6,2) unsigned NOT NULL,
  `FK_Prodotto` int(11) NOT NULL,
  `FK_UtenteMagazziniere` varchar(30) NOT NULL,
  `FK_PuntoVendita` int(11) NOT NULL,
  `FK_Fattura` int(11) NOT NULL,
  FOREIGN KEY (`FK_Fattura`) REFERENCES `cnFattura` (`ID_Fattura`),
  FOREIGN KEY (`FK_Prodotto`) REFERENCES `cnProdotto` (`ID_Prodotto`),
  FOREIGN KEY (`FK_PuntoVendita`) REFERENCES `cnPuntoVendita` (`ID_PuntoVendita`),
  FOREIGN KEY (`FK_UtenteMagazziniere`) REFERENCES `cnUtente` (`Username`)
)
```

#### Vendita
Stores the entries of sales receipts, regardless they have been cancelled or not (`Stornato` attribute). Cancelled entries are kept to track how many mistakes cashiers do. Valid entries are used to calculate current inventory status and market earnings.
```sql
CREATE TABLE cnVendita (
  `ID_Vendita` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `DataOra` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Quantita` int(11) unsigned NOT NULL,
  `PrezzoVendita` decimal(6,2) unsigned DEFAULT NULL,
  `Stornato` tinyint(1) NOT NULL DEFAULT '0',
  `FK_UtenteCassiere` varchar(30) NOT NULL,
  `FK_Cassa` int(11) NOT NULL,
  `FK_Prodotto` int(11) NOT NULL,
  `FK_Fattura` int(11) NOT NULL,
  FOREIGN KEY (`FK_Cassa`) REFERENCES `cnCassa` (`ID_Cassa`),
  FOREIGN KEY (`FK_Fattura`) REFERENCES `cnFattura` (`ID_Fattura`),
  FOREIGN KEY (`FK_Prodotto`) REFERENCES `cnProdotto` (`ID_Prodotto`),
  FOREIGN KEY (`FK_UtenteCassiere`) REFERENCES `cnUtente` (`Username`)
)
```

### Triggers
Those triggers are used to update customer's fidelity card balance when a product is scanned (+) or removed from the receipt (-).
```sql
CREATE TRIGGER PtiCartaOnInsert
AFTER INSERT ON cnVendita
FOR EACH ROW
BEGIN
  IF NEW.Stornato = 0 THEN
    UPDATE cnCartaFedelta
    SET SaldoPunti = (SaldoPunti + (NEW.PrezzoVendita * NEW.Quantita))
    WHERE FK_Utente = (SELECT FK_Utente FROM cnFattura WHERE ID_Fattura = NEW.FK_Fattura);
  end if;
end
```

```sql
CREATE TRIGGER PtiCartaOnUpdate
AFTER UPDATE ON cnVendita
FOR EACH ROW
BEGIN
  IF OLD.Stornato = 0 AND NEW.Stornato = 1 THEN
    UPDATE cnCartaFedelta
    SET SaldoPunti = (SaldoPunti - (NEW.PrezzoVendita * NEW.Quantita))
    WHERE FK_Utente = (SELECT FK_Utente FROM cnFattura WHERE ID_Fattura = NEW.FK_Fattura);
  end if;
end
```
